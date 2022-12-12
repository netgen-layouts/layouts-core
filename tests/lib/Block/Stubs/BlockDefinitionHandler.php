<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\Stubs;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandler as BaseBlockDefinitionHandler;
use Netgen\Layouts\Block\DynamicParameters;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType;

final class BlockDefinitionHandler extends BaseBlockDefinitionHandler
{
    /**
     * @var string[]
     */
    private array $parameterGroups;

    private bool $isContextual;

    /**
     * @param string[] $parameterGroups
     */
    public function __construct(array $parameterGroups = [], bool $isContextual = false)
    {
        $this->parameterGroups = $parameterGroups;
        $this->isContextual = $isContextual;
    }

    /**
     * @return array<string, \Netgen\Layouts\Parameters\ParameterDefinition>
     */
    public function getParameterDefinitions(): array
    {
        return [
            'css_class' => ParameterDefinition::fromArray(
                [
                    'name' => 'css_class',
                    'type' => new ParameterType\TextLineType(),
                    'isRequired' => true,
                    'isReadOnly' => false,
                    'defaultValue' => 'some-class',
                    'groups' => $this->parameterGroups,
                    'options' => [
                        'translatable' => false,
                    ],
                ],
            ),
            'css_id' => ParameterDefinition::fromArray(
                [
                    'name' => 'css_id',
                    'type' => new ParameterType\TextLineType(),
                    'isRequired' => false,
                    'isReadOnly' => false,
                    'groups' => $this->parameterGroups,
                    'options' => [
                        'translatable' => false,
                    ],
                ],
            ),
        ];
    }

    public function getDynamicParameters(DynamicParameters $params, Block $block): void
    {
        $params['definition_param'] = 'definition_value';
        $params['closure_param'] = static fn (): string => 'closure_value';
    }

    public function isContextual(Block $block): bool
    {
        return $this->isContextual;
    }
}
