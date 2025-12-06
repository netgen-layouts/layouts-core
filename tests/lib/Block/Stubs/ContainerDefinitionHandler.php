<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\Stubs;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\Layouts\Block\BlockDefinition\ContainerDefinitionHandlerInterface;
use Netgen\Layouts\Block\DynamicParameters;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use Netgen\Layouts\Parameters\ParameterType;
use Netgen\Layouts\Tests\Stubs\ParameterBuilderTrait;

final class ContainerDefinitionHandler extends BlockDefinitionHandler implements ContainerDefinitionHandlerInterface
{
    use ParameterBuilderTrait;

    /**
     * @param string[] $parameterGroups
     * @param string[] $placeholderIdentifiers
     */
    public function __construct(
        private array $parameterGroups = [],
        private array $placeholderIdentifiers = ['left', 'right'],
    ) {}

    public function buildParameters(ParameterBuilderInterface $builder): void
    {
        $builder->add(
            'css_class',
            ParameterType\TextLineType::class,
            [
                'default_value' => 'some-class',
                'groups' => $this->parameterGroups,
            ],
        );

        $builder->add(
            'css_id',
            ParameterType\TextLineType::class,
            [
                'groups' => $this->parameterGroups,
            ],
        );
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
