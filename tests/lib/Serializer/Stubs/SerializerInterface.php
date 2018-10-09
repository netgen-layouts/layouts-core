<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Serializer\Stubs;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface as BaseSerializerInterface;

interface SerializerInterface extends BaseSerializerInterface, NormalizerInterface
{
}
