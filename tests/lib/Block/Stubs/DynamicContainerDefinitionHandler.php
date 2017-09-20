<?php

namespace Netgen\BlockManager\Tests\Block\Stubs;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\DynamicContainerHandler as BaseDynamicContainerHandler;
use Netgen\BlockManager\Block\DynamicParameters;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Tests\Parameters\Stubs\Parameter;

class DynamicContainerDefinitionHandler extends BaseDynamicContainerHandler
{
    /**
     * @var array
     */
    private $parameterGroups = array();

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
                    'options' => array(
                        'translatable' => false,
                    ),
                ),
                true
            ),
            'css_id' => new Parameter(
                array(
                    'name' => 'css_id',
                    'type' => new ParameterType\TextLineType(),
                    'groups' => $this->parameterGroups,
                    'options' => array(
                        'translatable' => false,
                    ),
                ),
                true
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
     * Adds the dynamic parameters to the $params object for the provided block.
     *
     * @param \Netgen\BlockManager\Block\DynamicParameters $params
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     */
    public function getDynamicParameters(DynamicParameters $params, Block $block)
    {
        $params['definition_param'] = 'definition_value';
    }
}
