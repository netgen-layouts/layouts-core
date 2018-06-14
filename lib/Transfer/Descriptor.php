<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Transfer;

/**
 * Descriptor contains version identifier, used in the Serializer output.
 */
abstract class Descriptor
{
    /**
     * Format version.
     *
     * @var int
     */
    public const FORMAT_VERSION = 1;
}
