<?php

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
    const FORMAT_VERSION = 1;
}
