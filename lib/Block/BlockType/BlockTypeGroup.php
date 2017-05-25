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
     * @param bool $onlyEnabled
     *
     * @return \Netgen\BlockManager\Block\BlockType\BlockType[]
     */
    public function getBlockTypes($onlyEnabled = false)
    {
        if (!$onlyEnabled) {
            return $this->blockTypes;
        }

        return array_filter(
            $this->blockTypes,
            function (BlockType $blockType) {
                return $blockType->isEnabled();
            }
        );
    }
}
