<?php

namespace Netgen\BlockManager\Tests\Block\Stubs;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandler as BaseContainerDefinitionHandler;
use Netgen\BlockManager\Block\DynamicParameters;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Tests\Parameters\Stubs\Parameter;

final class ContainerDefinitionHandler extends BaseContainerDefinitionHandler
{
    /**
     * @var array
     */
    private $parameterGroups = array();

    /**
     * @var array
     */
    private $placeholderIdentifiers = array();

    /**
     * Constructor.
     *
     * @param array $parameterGroups
     * @param array $placeholderIdentifiers
     */
    public function __construct($parameterGroups = array(), $placeholderIdentifiers = array('left', 'right'))
    {
        $this->parameterGroups = $parameterGroups;
        $this->placeholderIdentifiers = $placeholderIdentifiers;
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
                    'defaultValue' => 'some-class',
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
     * Returns if this block definition is a container.
     *
     * @return bool
     */
    public function isContainer()
    {
        return true;
    }

    /**
     * Returns placeholder identifiers.
     *
     * @return array
     */
    public function getPlaceholderIdentifiers()
    {
        return $this->placeholderIdentifiers;
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
