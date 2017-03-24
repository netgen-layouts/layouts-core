<?php

namespace Netgen\BlockManager\Block\BlockType;

use Netgen\BlockManager\ValueObject;

class BlockTypeGroup extends ValueObject
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Netgen\BlockManager\Block\BlockType\BlockType[]
     */
    protected $blockTypes = array();

    /**
     * Returns the block type group identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns the block type group name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the block types in this group.
     *
     * @return \Netgen\BlockManager\Block\BlockType\BlockType[]
     */
    public function getBlockTypes()
    {
        return $this->blockTypes;
    }
}
