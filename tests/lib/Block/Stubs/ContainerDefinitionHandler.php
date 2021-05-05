<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\Stubs;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\Layouts\Block\BlockDefinition\ContainerDefinitionHandlerInterface;
use Netgen\Layouts\Block\DynamicParameters;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType;

final class ContainerDefinitionHandler extends BlockDefinitionHandler implements ContainerDefinitionHandlerInterface
{
    /**
     * @var string[]
     */
    private array $parameterGroups;

    /**
     * @var string[]
     */
    private array $placeholderIdentifiers;

    /**
     * @param string[] $parameterGroups
     * @param string[] $placeholderIdentifiers
     */
    public function __construct(array $parameterGroups = [], array $placeholderIdentifiers = ['left', 'right'])
    {
        $this->parameterGroups = $parameterGroups;
        $this->placeholderIdentifiers = $placeholderIdentifiers;
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
                    'isRequired' => false,
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
                    'groups' => $this->parameterGroups,
                    'options' => [
                        'translatable' => false,
                    ],
                ],
            ),
        ];
    }

    public function getPlaceholderIdentifiers(): array
    {
        return $this->placeholderIdentifiers;
    }

    public function getDynamicParameters(DynamicParameters $params, Block $block): void
    {
        $params['definition_param'] = 'definition_value';
    }
}
