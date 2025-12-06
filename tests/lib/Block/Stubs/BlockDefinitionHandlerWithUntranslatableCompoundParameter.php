<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\Stubs;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandler as BaseBlockDefinitionHandler;
use Netgen\Layouts\Block\DynamicParameters;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use Netgen\Layouts\Parameters\ParameterType;
use Netgen\Layouts\Tests\Stubs\ParameterBuilderTrait;

final class BlockDefinitionHandlerWithUntranslatableCompoundParameter extends BaseBlockDefinitionHandler
{
    use ParameterBuilderTrait;

    /**
     * @param string[] $parameterGroups
     */
    public function __construct(
        private array $parameterGroups = [],
    ) {}

    public function buildParameters(ParameterBuilderInterface $builder): void
    {
        $builder->add(
            'compound',
            ParameterType\Compound\BooleanType::class,
            [
                'groups' => $this->parameterGroups,
                'translatable' => false,
            ],
        );

        $builder->get('compound')->add(
            'inner',
            ParameterType\TextLineType::class,
            [
                'groups' => $this->parameterGroups,
                'translatable' => false,
            ],
        );

        $builder->add(
            'css_class',
            ParameterType\TextLineType::class,
            [
                'default_value' => 'some-class',
                'groups' => $this->parameterGroups,
                'translatable' => true,
            ],
        );

        $builder->add(
            'other',
            ParameterType\TextLineType::class,
            [
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
    }
}
