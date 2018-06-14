<?php

declare(strict_types=1);

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

    public function __construct(array $parameterGroups = [], bool $isContextual = false)
    {
        $this->parameterGroups = $parameterGroups;
        $this->isContextual = $isContextual;
    }

    public function getParameterDefinitions(): array
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

    public function getDynamicParameters(DynamicParameters $params, Block $block): void
    {
        $params['definition_param'] = 'definition_value';
        $params['closure_param'] = function (): string {
            return 'closure_value';
        };
    }

    public function isContextual(Block $block): bool
    {
        return $this->isContextual;
    }
}
