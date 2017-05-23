<?php

namespace Netgen\BlockManager\Tests\Block\Stubs;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\DynamicContainerHandler as BaseDynamicContainerHandler;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Tests\Parameters\Stubs\Parameter;

class DynamicContainerDefinitionHandler extends BaseDynamicContainerHandler
{
    /**
     * @var array
     */
    protected $parameterGroups = array();

    /**
     * Constructor.
     *
     * @param array $parameterGroups
     */
    public function __construct($parameterGroups = array())
    {
        $this->parameterGroups = $parameterGroups;
    }

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
            'css_class' => new Parameter(
                array(
                    'name' => 'css_class',
                    'type' => new ParameterType\TextLineType(),
                    'default_value' => 'some-class',
                    'groups' => $this->parameterGroups,
                )
            ),
            'css_id' => new Parameter(
                array(
                    'name' => 'css_id',
                    'type' => new ParameterType\TextLineType(),
                    'groups' => $this->parameterGroups,
                )
            ),
        );
    }

    /**
     * Returns if this block definition is a dynamic container.
     *
     * @return bool
     */
    public function isDynamicContainer()
    {
        return true;
    }

    /**
     * Returns the array of dynamic parameters provided by this block definition.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return array
     */
    public function getDynamicParameters(Block $block)
    {
        return array('definition_param' => 'definition_value');
    }
}
