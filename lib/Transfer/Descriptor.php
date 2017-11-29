<?php

namespace Netgen\BlockManager\Transfer;

/**
 * Descriptor contains format identifiers and versions, used in the Serializer output
 * and to determine if the provided serialized data is acceptable for import.
 */
final class Descriptor
{
    /**
     * Layout format identifier.
     *
     * @var string
     */
    const LAYOUT_FORMAT_TYPE = 'layout';

    /**
     * Layout format version.
     *
     * @var int
     */
    const LAYOUT_FORMAT_VERSION = 1;
}
