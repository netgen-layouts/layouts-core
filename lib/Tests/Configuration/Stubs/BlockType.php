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
     */
    public function __construct($identifier)
    {
        parent::__construct(
            $identifier,
            $identifier,
            new BlockDefinition($identifier),
            array()
        );
    }
}
