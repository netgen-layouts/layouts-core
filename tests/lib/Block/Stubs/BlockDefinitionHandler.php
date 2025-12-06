<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\Stubs;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandler as BaseBlockDefinitionHandler;
use Netgen\Layouts\Block\DynamicParameters;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use Netgen\Layouts\Parameters\ParameterType;
use Netgen\Layouts\Tests\Stubs\ParameterBuilderTrait;

final class BlockDefinitionHandler extends BaseBlockDefinitionHandler
{
    use ParameterBuilderTrait;

    /**
     * @param string[] $parameterGroups
     */
    public function __construct(
        private array $parameterGroups = [],
        private bool $isContextual = false,
    ) {}

    public function buildParameters(ParameterBuilderInterface $builder): void
    {
        $builder->add(
            'css_class',
            ParameterType\TextLineType::class,
            [
                'required' => true,
                'default_value' => 'some-class',
                'groups' => $this->parameterGroups,
                'translatable' => false,
            ],
        );

        $builder->add(
            'css_id',
            ParameterType\TextLineType::class,
            [
                'groups' => $this->parameterGroups,
                'translatable' => false,
            ],
        );
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
