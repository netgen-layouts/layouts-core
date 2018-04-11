<?php

namespace Netgen\BlockManager\Tests\Block\Stubs;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\DynamicContainerHandler as BaseDynamicContainerHandler;
use Netgen\BlockManager\Block\DynamicParameters;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;

final class DynamicContainerDefinitionHandler extends BaseDynamicContainerHandler
{
    /**
     * @var array
     */
    private $parameterGroups = array();

    public function __construct($parameterGroups = array())
    {
        $this->parameterGroups = $parameterGroups;
    }

    public function getParameterDefinitions()
    {
        return array(
            'css_class' => new ParameterDefinition(
                array(
                    'name' => 'css_class',
                    'type' => new ParameterType\TextLineType(),
                    'default_value' => 'some-class',
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

    public function isDynamicContainer()
    {
        return true;
    }

    public function getDynamicParameters(DynamicParameters $params, Block $block)
    {
        $params['definition_param'] = 'definition_value';
    }
}
