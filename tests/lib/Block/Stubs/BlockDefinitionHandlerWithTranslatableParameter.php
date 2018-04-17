<?php

namespace Netgen\BlockManager\Tests\Block\Stubs;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler as BaseBlockDefinitionHandler;
use Netgen\BlockManager\Block\DynamicParameters;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;

final class BlockDefinitionHandlerWithTranslatableParameter extends BaseBlockDefinitionHandler
{
    /**
     * @var array
     */
    private $parameterGroups = [];

    public function __construct($parameterGroups = [])
    {
        $this->parameterGroups = $parameterGroups;
    }

    public function getParameterDefinitions()
    {
        return [
            'css_class' => new ParameterDefinition(
                [
                    'name' => 'css_class',
                    'type' => new ParameterType\TextLineType(),
                    'defaultValue' => 'some-class',
                    'groups' => $this->parameterGroups,
                    'options' => [
                        'translatable' => true,
                    ],
                ]
            ),
            'css_id' => new ParameterDefinition(
                [
                    'name' => 'css_id',
                    'type' => new ParameterType\TextLineType(),
                    'groups' => $this->parameterGroups,
                    'options' => [
                        'translatable' => false,
                    ],
                ]
            ),
        ];
    }

    public function getDynamicParameters(DynamicParameters $params, Block $block)
    {
        $params['definition_param'] = 'definition_value';
    }
}
