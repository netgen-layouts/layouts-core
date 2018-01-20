<?php

namespace Netgen\BlockManager\Tests\Block\Stubs;

use Netgen\BlockManager\Block\BlockType\BlockType as BaseBlockType;

final class BlockType extends BaseBlockType
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
