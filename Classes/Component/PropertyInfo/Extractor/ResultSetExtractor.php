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
    /**
     * @var ReflectionExtractor
     */
    private $reflectionExtractor;

    /**
     * ResultSetExtractor constructor.
     */
    public function __construct()
    {
        $this->reflectionExtractor = new ReflectionExtractor();
    }

    /**
     * @param string $class
     * @param string $property
     * @param array $context
     * @return array|Type[]|null
     */
    public function getTypes($class, $property, array $context = [])
    {
        if (is_a($class, ResultSet::class, true) && 'results' === $property) {
            return [
                new Type(Type::BUILTIN_TYPE_OBJECT, true, Result::class . '[]')
            ];
        }
        if (is_a($class, Result::class, true) && 'nodes' === $property) {
            return [
                new Type(Type::BUILTIN_TYPE_OBJECT, true, Node::class . '[]')
            ];
        }
        return $this->reflectionExtractor->getTypes($class, $property, $context);
    }
}
