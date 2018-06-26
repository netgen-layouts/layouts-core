<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Serializer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @deprecated Replace with NormalizerAwareTrait from Symfony when support for Symfony 2.8 ends.
 */
abstract class Normalizer
{
    /**
     * @var \Symfony\Component\Serializer\Normalizer\NormalizerInterface
     */
    protected $normalizer;

    /**
     * Sets the serializer.
     */
    public function setNormalizer(NormalizerInterface $normalizer): void
    {
        $this->normalizer = $normalizer;
    }
}
