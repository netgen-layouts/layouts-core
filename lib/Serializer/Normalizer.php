<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Serializer;

use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @deprecated Replace with NormalizerAwareTrait from Symfony when support for Symfony 2.8 ends.
 */
abstract class Normalizer implements SerializerAwareInterface
{
    protected $normalizer;

    /**
     * Sets the serializer.
     */
    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->normalizer = $serializer;
    }
}
