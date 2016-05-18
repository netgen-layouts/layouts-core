<?php

namespace Netgen\BlockManager\Configuration\BlockType;

class BlockTypeGroup
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $blockTypes = array();

    /**
     * Constructor.
     *
     * @param string $identifier
     * @param bool $enabled
     * @param string $name
     * @param array $blockTypes
     */
    public function __construct($identifier, $enabled, $name, array $blockTypes = array())
    {
        $this->identifier = $identifier;
        $this->enabled = $enabled;
        $this->name = $name;
        $this->blockTypes = $blockTypes;
    }

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
     * Returns if the block type group is enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
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
     * Returns the identifiers of block types in this group.
     *
     * @return array
     */
    public function getBlockTypes()
    {
        return $this->blockTypes;
    }
}
