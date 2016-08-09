<?php

namespace Netgen\BlockManager\Tests\Block\Stubs;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Block\BlockDefinitionInterface;

class BlockDefinition implements BlockDefinitionInterface
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var array
     */
    protected $viewTypes;

    /**
     * Constructor.
     *
     * @param string $identifier
     * @param array $viewTypes
     */
    public function __construct($identifier, array $viewTypes = array())
    {
        $this->identifier = $identifier;
        $this->viewTypes = $viewTypes;
    }

    /**
     * Returns block definition identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface
     */
    public function getHandler()
    {
        return new BlockDefinitionHandler();
    }

    /**
     * Returns the block definition configuration.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration
     */
    public function getConfig()
    {
        $viewTypes = array();
        foreach ($this->viewTypes as $viewType => $itemTypes) {
            $itemViewTypes = array();
            foreach ($itemTypes as $itemType) {
                $itemViewTypes[$itemType] = new ItemViewType($itemType, $itemType);
            }

            $viewTypes[$viewType] = new ViewType(
                $viewType,
                $viewType,
                $itemViewTypes
            );
        }

        return new Configuration($this->identifier, array(), $viewTypes);
    }
}
