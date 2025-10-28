<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer;

/**
 * Descriptor contains version identifier, used in the Serializer output.
 */
abstract class Descriptor
{
    /**
     * Format version.
     */
    final public const int FORMAT_VERSION = 1;
}
