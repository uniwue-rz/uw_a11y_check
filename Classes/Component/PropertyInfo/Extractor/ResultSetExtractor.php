<?php

namespace UniWue\UwA11yCheck\Component\PropertyInfo\Extractor;

use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type;
use UniWue\UwA11yCheck\Check\Result;
use UniWue\UwA11yCheck\Check\Result\Node;
use UniWue\UwA11yCheck\Check\ResultSet;

/**
 * Class ResultSetExtractor
 */
class ResultSetExtractor implements PropertyTypeExtractorInterface
{
    private ReflectionExtractor $reflectionExtractor;

    /**
     * ResultSetExtractor constructor.
     */
    public function __construct()
    {
        $this->reflectionExtractor = new ReflectionExtractor();
    }

    /**
     * @return array|Type[]|null
     */
    public function getTypes(string $class, string $property, array $context = []): ?array
    {
        if (is_a($class, ResultSet::class, true) && $property === 'results') {
            return [
                new Type(Type::BUILTIN_TYPE_OBJECT, true, Result::class . '[]'),
            ];
        }
        if (is_a($class, Result::class, true) && $property === 'nodes') {
            return [
                new Type(Type::BUILTIN_TYPE_OBJECT, true, Node::class . '[]'),
            ];
        }
        return $this->reflectionExtractor->getTypes($class, $property, $context);
    }
}
