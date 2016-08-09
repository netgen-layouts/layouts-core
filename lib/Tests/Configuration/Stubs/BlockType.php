<?php

namespace Netgen\BlockManager\Tests\Configuration\Stubs;

use Netgen\BlockManager\Configuration\BlockType\BlockType as BaseBlockType;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;

class BlockType extends BaseBlockType
{
    /**
     * Constructor.
     *
     * @param string $identifier
     * @param bool $enabled
     */
    public function __construct($identifier, $enabled = true)
    {
        parent::__construct(
            $identifier,
            $enabled,
            $identifier,
            new BlockDefinition($identifier),
            array()
        );
    }
}
