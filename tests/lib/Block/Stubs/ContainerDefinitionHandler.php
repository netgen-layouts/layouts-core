<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block\Stubs;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandlerInterface;
use Netgen\BlockManager\Block\DynamicParameters;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;

final class ContainerDefinitionHandler extends BlockDefinitionHandler implements ContainerDefinitionHandlerInterface
{
    /**
     * @var array
     */
    private $parameterGroups;

    /**
     * @var array
     */
    private $placeholderIdentifiers;

    public function __construct(array $parameterGroups = [], array $placeholderIdentifiers = ['left', 'right'])
    {
        $this->parameterGroups = $parameterGroups;
        $this->placeholderIdentifiers = $placeholderIdentifiers;
    }

    public function getParameterDefinitions(): array
    {
        return [
            'css_class' => ParameterDefinition::fromArray(
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
