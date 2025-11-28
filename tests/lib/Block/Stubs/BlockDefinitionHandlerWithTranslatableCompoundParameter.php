<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\Stubs;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandler as BaseBlockDefinitionHandler;
use Netgen\Layouts\Block\DynamicParameters;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType;

final class BlockDefinitionHandlerWithTranslatableCompoundParameter extends BaseBlockDefinitionHandler
{
    /**
     * @param string[] $parameterGroups
     */
    public function __construct(
        private array $parameterGroups = [],
    ) {}

    /**
     * @return array<string, \Netgen\Layouts\Parameters\ParameterDefinition>
     */
    public function getParameterDefinitions(): array
    {
        $compoundDefinition = ParameterDefinition::fromArray(
            [
                'name' => 'compound',
                'type' => new ParameterType\Compound\BooleanType(),
                'isRequired' => false,
                'defaultValue' => null,
                'label' => null,
                'groups' => $this->parameterGroups,
                'options' => [
                    'translatable' => true,
                ],
                'parameterDefinitions' => [
                    'inner' => ParameterDefinition::fromArray(
                        [
                            'name' => 'inner',
                            'type' => new ParameterType\TextLineType(),
                            'isRequired' => false,
                            'defaultValue' => null,
                            'label' => null,
                            'groups' => $this->parameterGroups,
                            'options' => [
                                'translatable' => true,
                            ],
                        ],
                    ),
                ],
            ],
        );

        return [
            'css_class' => ParameterDefinition::fromArray(
                [
                    'name' => 'css_class',
                    'type' => new ParameterType\TextLineType(),
                    'isRequired' => false,
                    'defaultValue' => 'some-class',
                    'label' => null,
                    'groups' => $this->parameterGroups,
                    'options' => [
                        'translatable' => true,
                    ],
                ],
            ),
            'css_id' => ParameterDefinition::fromArray(
                [
                    'name' => 'css_id',
                    'type' => new ParameterType\TextLineType(),
                    'isRequired' => false,
                    'defaultValue' => null,
                    'label' => null,
                    'groups' => $this->parameterGroups,
                    'options' => [
                        'translatable' => false,
                    ],
                ],
            ),
            'compound' => $compoundDefinition,
        ];
    }

    public function getDynamicParameters(DynamicParameters $params, Block $block): void
    {
        $params['definition_param'] = 'definition_value';
    }
}
