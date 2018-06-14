<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Serializer;

use Symfony\Component\Serializer\SerializerInterface;

/**
 * @deprecated Replace with NormalizerAwareTrait from Symfony when support for Symfony 2.8 ends.
 */
trait SerializerAwareTrait
{
    /**
     * @var \Symfony\Component\Serializer\SerializerInterface&\Symfony\Component\Serializer\Normalizer\NormalizerInterface
     */
    private $serializer;

    /**
     * Sets the serializer.
     */
    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }
}
