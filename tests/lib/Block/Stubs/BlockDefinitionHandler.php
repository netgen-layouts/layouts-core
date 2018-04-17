<?php

namespace Netgen\BlockManager\Tests\Block\Stubs;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler as BaseBlockDefinitionHandler;
use Netgen\BlockManager\Block\DynamicParameters;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;

class BlockDefinitionHandler extends BaseBlockDefinitionHandler
{
    /**
     * @var array
     */
    private $parameterGroups = [];

    /**
     * @var bool
     */
    private $isContextual;

    public function __construct($parameterGroups = [], $isContextual = false)
    {
        $this->parameterGroups = $parameterGroups;
        $this->isContextual = $isContextual;
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
                        'translatable' => false,
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
        $params['closure_param'] = function () {
            return 'closure_value';
        };
    }

    public function isContextual(Block $block)
    {
        return $this->isContextual;
    }
}
