<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\PropertyInfo\Extractor;

use phpDocumentor\Reflection\Types\ContextFactory;
use PHPStan\PhpDocParser\Ast\PhpDoc\InvalidTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use PHPStan\PhpDocParser\ParserConfig;
use Symfony\Component\PropertyInfo\PhpStan\NameScope;
use Symfony\Component\PropertyInfo\PhpStan\NameScopeFactory;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type as LegacyType;
use Symfony\Component\PropertyInfo\Util\PhpStanTypeHelper;
use Symfony\Component\TypeInfo\Exception\UnsupportedException;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\TypeContext\TypeContextFactory;
use Symfony\Component\TypeInfo\TypeResolver\StringTypeResolver;

/**
 * Extracts data using PHPStan parser.
 *
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
final class PhpStanExtractor implements PropertyTypeExtractorInterface, ConstructorArgumentTypeExtractorInterface
{
    private const PROPERTY = 0;
    private const ACCESSOR = 1;
    private const MUTATOR = 2;

    private PhpDocParser $phpDocParser;
    private Lexer $lexer;
    private NameScopeFactory $nameScopeFactory;

    private StringTypeResolver $stringTypeResolver;
    private TypeContextFactory $typeContextFactory;

    /** @var array<string, array{PhpDocNode|null, int|null, string|null, string|null}> */
    private array $docBlocks = [];
    private PhpStanTypeHelper $phpStanTypeHelper;
    private array $mutatorPrefixes;
    private array $accessorPrefixes;
    private array $arrayMutatorPrefixes;

    /** @var array<string, NameScope> */
    private array $contexts = [];

    /**
     * @param list<string>|null $mutatorPrefixes
     * @param list<string>|null $accessorPrefixes
     * @param list<string>|null $arrayMutatorPrefixes
     */
    public function __construct(?array $mutatorPrefixes = null, ?array $accessorPrefixes = null, ?array $arrayMutatorPrefixes = null, private bool $allowPrivateAccess = true)
    {
        if (!class_exists(ContextFactory::class)) {
            throw new \LogicException(\sprintf('Unable to use the "%s" class as the "phpdocumentor/type-resolver" package is not installed. Try running composer require "phpdocumentor/type-resolver".', __CLASS__));
        }

        if (!class_exists(PhpDocParser::class)) {
            throw new \LogicException(\sprintf('Unable to use the "%s" class as the "phpstan/phpdoc-parser" package is not installed. Try running composer require "phpstan/phpdoc-parser".', __CLASS__));
        }

        $this->phpStanTypeHelper = new PhpStanTypeHelper();
        $this->mutatorPrefixes = $mutatorPrefixes ?? ReflectionExtractor::$defaultMutatorPrefixes;
        $this->accessorPrefixes = $accessorPrefixes ?? ReflectionExtractor::$defaultAccessorPrefixes;
        $this->arrayMutatorPrefixes = $arrayMutatorPrefixes ?? ReflectionExtractor::$defaultArrayMutatorPrefixes;

        if (class_exists(ParserConfig::class)) {
            $parserConfig = new ParserConfig([]);
            $this->phpDocParser = new PhpDocParser($parserConfig, new TypeParser($parserConfig, new ConstExprParser($parserConfig)), new ConstExprParser($parserConfig));
            $this->lexer = new Lexer($parserConfig);
        } else {
            $this->phpDocParser = new PhpDocParser(new TypeParser(new ConstExprParser()), new ConstExprParser());
            $this->lexer = new Lexer();
        }
        $this->nameScopeFactory = new NameScopeFactory();
        $this->stringTypeResolver = new StringTypeResolver();
        $this->typeContextFactory = new TypeContextFactory($this->stringTypeResolver);
    }

    public function getTypes(string $class, string $property, array $context = []): ?array
    {
        /** @var PhpDocNode|null $docNode */
        [$docNode, $source, $prefix, $declaringClass] = $this->getDocBlock($class, $property);
        if (null === $docNode) {
            return null;
        }

        switch ($source) {
            case self::PROPERTY:
                $tag = '@var';
                break;

            case self::ACCESSOR:
                $tag = '@return';
                break;

            case self::MUTATOR:
                $tag = '@param';
                break;
        }

        $parentClass = null;
        $types = [];
        foreach ($docNode->getTagsByName($tag) as $tagDocNode) {
            if ($tagDocNode->value instanceof InvalidTagValueNode) {
                continue;
            }

            if (
                $tagDocNode->value instanceof ParamTagValueNode
                && null === $prefix
                && $tagDocNode->value->parameterName !== '$'.$property
            ) {
                continue;
            }

            $nameScope ??= $this->contexts[$class.'/'.$declaringClass] ??= $this->nameScopeFactory->create($class, $declaringClass);
            foreach ($this->phpStanTypeHelper->getTypes($tagDocNode->value, $nameScope) as $type) {
                switch ($type->getClassName()) {
                    case 'self':
                    case 'static':
                        $resolvedClass = $class;
                        break;

                    case 'parent':
                        if (false !== $resolvedClass = $parentClass ??= get_parent_class($class)) {
                            break;
                        }
                        // no break

                    default:
                        $types[] = $type;
                        continue 2;
                }

                $types[] = new LegacyType(LegacyType::BUILTIN_TYPE_OBJECT, $type->isNullable(), $resolvedClass, $type->isCollection(), $type->getCollectionKeyTypes(), $type->getCollectionValueTypes());
            }
        }

        if (!isset($types[0])) {
            return null;
        }

        if (!\in_array($prefix, $this->arrayMutatorPrefixes, true)) {
            return $types;
        }

        return [new LegacyType(LegacyType::BUILTIN_TYPE_ARRAY, false, null, true, new LegacyType(LegacyType::BUILTIN_TYPE_INT), $types[0])];
    }

    /**
     * @return LegacyType[]|null
     */
    public function getTypesFromConstructor(string $class, string $property): ?array
    {
        if (null === $tagDocNode = $this->getDocBlockFromConstructor($class, $property)) {
            return null;
        }

        $types = [];
        foreach ($this->phpStanTypeHelper->getTypes($tagDocNode, $this->nameScopeFactory->create($class)) as $type) {
            $types[] = $type;
        }

        if (!isset($types[0])) {
            return null;
        }

        return $types;
    }

    public function getType(string $class, string $property, array $context = []): ?Type
    {
        /** @var PhpDocNode|null $docNode */
        [$docNode, $source, $prefix, $declaringClass] = $this->getDocBlock($class, $property);

        if (null === $docNode) {
            return null;
        }

        $typeContext = $this->typeContextFactory->createFromClassName($class, $declaringClass);

        $tag = match ($source) {
            self::PROPERTY => '@var',
            self::ACCESSOR => '@return',
            self::MUTATOR => '@param',
            default => 'invalid',
        };

        $types = [];

        foreach ($docNode->getTagsByName($tag) as $tagDocNode) {
            if (!$tagDocNode->value instanceof ParamTagValueNode && !$tagDocNode->value instanceof ReturnTagValueNode && !$tagDocNode->value instanceof VarTagValueNode) {
                continue;
            }

            if ($tagDocNode->value instanceof ParamTagValueNode && null === $prefix && $tagDocNode->value->parameterName !== '$'.$property) {
                continue;
            }

            try {
                $types[] = $this->stringTypeResolver->resolve((string) $tagDocNode->value->type, $typeContext);
            } catch (UnsupportedException) {
            }
        }

        if (!$type = $types[0] ?? null) {
            return null;
        }

        if (!\in_array($prefix, $this->arrayMutatorPrefixes, true)) {
            return $type;
        }

        return Type::list($type);
    }

    public function getTypeFromConstructor(string $class, string $property): ?Type
    {
        if (!$tagDocNode = $this->getDocBlockFromConstructor($class, $property)) {
            return null;
        }

        $typeContext = $this->typeContextFactory->createFromClassName($class);

        return $this->stringTypeResolver->resolve((string) $tagDocNode->type, $typeContext);
    }

    private function getDocBlockFromConstructor(string $class, string $property): ?ParamTagValueNode
    {
        try {
            $reflectionClass = new \ReflectionClass($class);
        } catch (\ReflectionException) {
            return null;
        }

        if (null === $reflectionConstructor = $reflectionClass->getConstructor()) {
            return null;
        }

        if (!$rawDocNode = $reflectionConstructor->getDocComment()) {
            return null;
        }

        $phpDocNode = $this->getPhpDocNode($rawDocNode);

        return $this->filterDocBlockParams($phpDocNode, $property);
    }

    private function filterDocBlockParams(PhpDocNode $docNode, string $allowedParam): ?ParamTagValueNode
    {
        $tags = array_values(array_filter($docNode->getTagsByName('@param'), fn ($tagNode) => $tagNode instanceof PhpDocTagNode && ('$'.$allowedParam) === $tagNode->value->parameterName));

        if (!$tags) {
            return null;
        }

        return $tags[0]->value;
    }

    /**
     * @return array{PhpDocNode|null, int|null, string|null, string|null}
     */
    private function getDocBlock(string $class, string $property): array
    {
        $propertyHash = $class.'::'.$property;

        if (isset($this->docBlocks[$propertyHash])) {
            return $this->docBlocks[$propertyHash];
        }

        $ucFirstProperty = ucfirst($property);

        if ([$docBlock, $source, $declaringClass] = $this->getDocBlockFromProperty($class, $property)) {
            $data = [$docBlock, $source, null, $declaringClass];
        } elseif ([$docBlock, $_, $declaringClass] = $this->getDocBlockFromMethod($class, $ucFirstProperty, self::ACCESSOR)) {
            $data = [$docBlock, self::ACCESSOR, null, $declaringClass];
        } elseif ([$docBlock, $prefix, $declaringClass] = $this->getDocBlockFromMethod($class, $ucFirstProperty, self::MUTATOR)) {
            $data = [$docBlock, self::MUTATOR, $prefix, $declaringClass];
        } else {
            $data = [null, null, null, null];
        }

        return $this->docBlocks[$propertyHash] = $data;
    }

    /**
     * @return array{PhpDocNode, int, string}|null
     */
    private function getDocBlockFromProperty(string $class, string $property): ?array
    {
        // Use a ReflectionProperty instead of $class to get the parent class if applicable
        try {
            $reflectionProperty = new \ReflectionProperty($class, $property);
        } catch (\ReflectionException) {
            return null;
        }

        if (!$this->canAccessMemberBasedOnItsVisibility($reflectionProperty)) {
            return null;
        }

        $reflector = $reflectionProperty->getDeclaringClass();

        foreach ($reflector->getTraits() as $trait) {
            if ($trait->hasProperty($property)) {
                return $this->getDocBlockFromProperty($trait->getName(), $property);
            }
        }

        // Type can be inside property docblock as `@var`
        $rawDocNode = $reflectionProperty->getDocComment();
        $phpDocNode = $rawDocNode ? $this->getPhpDocNode($rawDocNode) : null;
        $source = self::PROPERTY;

        if (!$phpDocNode?->getTagsByName('@var')) {
            $phpDocNode = null;
        }

        // or in the constructor as `@param` for promoted properties
        if (!$phpDocNode && $reflectionProperty->isPromoted()) {
            $constructor = new \ReflectionMethod($class, '__construct');
            $rawDocNode = $constructor->getDocComment();
            $phpDocNode = $rawDocNode ? $this->getPhpDocNode($rawDocNode) : null;
            $source = self::MUTATOR;
        }

        if (!$phpDocNode) {
            return null;
        }

        return [$phpDocNode, $source, $reflectionProperty->class];
    }

    /**
     * @return array{PhpDocNode, string, string}|null
     */
    private function getDocBlockFromMethod(string $class, string $ucFirstProperty, int $type): ?array
    {
        $prefixes = self::ACCESSOR === $type ? $this->accessorPrefixes : $this->mutatorPrefixes;
        $prefix = null;

        foreach ($prefixes as $prefix) {
            $methodName = $prefix.$ucFirstProperty;

            try {
                $reflectionMethod = new \ReflectionMethod($class, $methodName);
                if ($reflectionMethod->isStatic()) {
                    continue;
                }

                if (
                    (
                        (self::ACCESSOR === $type && 0 === $reflectionMethod->getNumberOfRequiredParameters())
                        || (self::MUTATOR === $type && $reflectionMethod->getNumberOfParameters() >= 1)
                    )
                    && $this->canAccessMemberBasedOnItsVisibility($reflectionMethod)
                ) {
                    break;
                }
            } catch (\ReflectionException) {
                // Try the next prefix if the method doesn't exist
            }
        }

        if (!isset($reflectionMethod)) {
            return null;
        }

        if (null === $rawDocNode = $reflectionMethod->getDocComment() ?: null) {
            return null;
        }

        $phpDocNode = $this->getPhpDocNode($rawDocNode);

        return [$phpDocNode, $prefix, $reflectionMethod->class];
    }

    private function getPhpDocNode(string $rawDocNode): PhpDocNode
    {
        $tokens = new TokenIterator($this->lexer->tokenize($rawDocNode));
        $phpDocNode = $this->phpDocParser->parse($tokens);
        $tokens->consumeTokenType(Lexer::TOKEN_END);

        return $phpDocNode;
    }

    private function canAccessMemberBasedOnItsVisibility(\ReflectionProperty|\ReflectionMethod $member): bool
    {
        return $this->allowPrivateAccess || $member->isPublic();
    }
}
