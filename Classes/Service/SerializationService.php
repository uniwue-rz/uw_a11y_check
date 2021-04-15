<?php

namespace UniWue\UwA11yCheck\Service;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use UniWue\UwA11yCheck\Component\PropertyInfo\Extractor\ResultSetExtractor;

/**
 * Class SerializationService
 */
class SerializationService
{
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * SerializationService constructor.
     */
    public function __construct()
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [
            new ArrayDenormalizer(),
            new ObjectNormalizer(null, null, null, new ResultSetExtractor()),
        ];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * Returns the serializer instance
     *
     * @return Serializer
     */
    public function getSerializer(): Serializer
    {
        return $this->serializer;
    }
}
