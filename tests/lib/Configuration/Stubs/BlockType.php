<?php

namespace Netgen\BlockManager\Tests\Configuration\Stubs;

use Netgen\BlockManager\Configuration\BlockType\BlockType as BaseBlockType;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;

class BlockType extends BaseBlockType
{
    /**
     * Constructor.
     *
     * @param array $properties
     */
    public function __construct(array $properties = array())
    {
        $properties['name'] = $properties['identifier'];
        $properties['definition'] = new BlockDefinition($properties['identifier']);

        parent::__construct($properties);
    }
}
