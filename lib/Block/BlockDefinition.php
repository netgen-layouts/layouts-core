<?php

namespace Netgen\BlockManager\Block;

use Netgen\BlockManager\Configuration\BlockDefinition\BlockDefinition as Config;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Parameters\Parameter;

abstract class BlockDefinition implements BlockDefinitionInterface
{
    /**
     * @var \Netgen\BlockManager\Configuration\BlockDefinition\BlockDefinition
     */
    protected $config;

    /**
     * Returns the array specifying block parameters.
     *
     * The keys are parameter identifiers.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return array(
            'css_id' => new Parameter\Text(),
            'css_class' => new Parameter\Text(),
        );
    }

    /**
     * Returns the array of dynamic parameters provided by this block definition.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param array $parameters
     *
     * @return array
     */
    public function getDynamicParameters(Block $block, array $parameters = array())
    {
        return array();
    }

    /**
     * Sets the block definition configuration.
     *
     * @param \Netgen\BlockManager\Configuration\BlockDefinition\BlockDefinition $config
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Returns the block definition configuration.
     *
     * @return \Netgen\BlockManager\Configuration\BlockDefinition\BlockDefinition
     */
    public function getConfig()
    {
        return $this->config;
    }
}
