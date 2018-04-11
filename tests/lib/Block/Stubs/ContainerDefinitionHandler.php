<?php

namespace Netgen\BlockManager\Tests\Block\Stubs;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandler as BaseContainerDefinitionHandler;
use Netgen\BlockManager\Block\DynamicParameters;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;

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

    public function __construct($parameterGroups = array(), $placeholderIdentifiers = array('left', 'right'))
    {
        $this->parameterGroups = $parameterGroups;
        $this->placeholderIdentifiers = $placeholderIdentifiers;
    }

    public function getParameterDefinitions()
    {
        return array(
            'css_class' => new ParameterDefinition(
                array(
                    'name' => 'css_class',
                    'type' => new ParameterType\TextLineType(),
                    'defaultValue' => 'some-class',
                    'groups' => $this->parameterGroups,
                    'options' => array(
                        'translatable' => false,
                    ),
                )
            ),
            'css_id' => new ParameterDefinition(
                array(
                    'name' => 'css_id',
                    'type' => new ParameterType\TextLineType(),
                    'groups' => $this->parameterGroups,
                    'options' => array(
                        'translatable' => false,
                    ),
                )
            ),
        );
    }

    public function isContainer()
    {
        return true;
    }

    public function getPlaceholderIdentifiers()
    {
        return $this->placeholderIdentifiers;
    }

    public function getDynamicParameters(DynamicParameters $params, Block $block)
    {
        $params['definition_param'] = 'definition_value';
    }
}
