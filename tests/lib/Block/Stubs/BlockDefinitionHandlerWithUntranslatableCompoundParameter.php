<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block\Stubs;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler as BaseBlockDefinitionHandler;
use Netgen\BlockManager\Block\DynamicParameters;
use Netgen\BlockManager\Parameters\CompoundParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;

final class BlockDefinitionHandlerWithUntranslatableCompoundParameter extends BaseBlockDefinitionHandler
{
    /**
     * @var array
     */
    private $parameterGroups;

    public function __construct(array $parameterGroups = [])
    {
        $this->parameterGroups = $parameterGroups;
    }

    public function getParameterDefinitions(): array
    {
        $compoundParam = CompoundParameterDefinition::fromArray(
            [
                'name' => 'compound',
                'type' => new ParameterType\Compound\BooleanType(),
                'groups' => $this->parameterGroups,
                'options' => [
                    'translatable' => false,
                ],
                'parameterDefinitions' => [
                    'inner' => ParameterDefinition::fromArray(
                        [
                            'name' => 'inner',
                            'type' => new ParameterType\TextLineType(),
                            'groups' => $this->parameterGroups,
                            'options' => [
                                'translatable' => false,
                            ],
                        ]
                    ),
                ],
            ]
        );

        return [
            'css_class' => ParameterDefinition::fromArray(
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
            'other' => ParameterDefinition::fromArray(
                [
                    'name' => 'other',
                    'type' => new ParameterType\TextLineType(),
                    'groups' => $this->parameterGroups,
                    'options' => [
                        'translatable' => false,
                    ],
                ]
            ),
            'css_id' => ParameterDefinition::fromArray(
                [
                    'name' => 'css_id',
                    'type' => new ParameterType\TextLineType(),
                    'groups' => $this->parameterGroups,
                    'options' => [
                        'translatable' => false,
                    ],
                ]
            ),
            'compound' => $compoundParam,
        ];
    }

    public function getDynamicParameters(DynamicParameters $params, Block $block): void
    {
        $params['definition_param'] = 'definition_value';
    }
}
