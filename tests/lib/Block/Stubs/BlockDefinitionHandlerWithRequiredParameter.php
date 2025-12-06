<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\Stubs;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandler as BaseBlockDefinitionHandler;
use Netgen\Layouts\Block\DynamicParameters;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use Netgen\Layouts\Parameters\ParameterType;
use Netgen\Layouts\Tests\Stubs\ParameterBuilderTrait;

final class BlockDefinitionHandlerWithRequiredParameter extends BaseBlockDefinitionHandler
{
    use ParameterBuilderTrait;

    public function buildParameters(ParameterBuilderInterface $builder): void
    {
        $builder->add(
            'css_class',
            ParameterType\TextLineType::class,
            [
                'required' => true,
                'translatable' => false,
            ],
        );

        $builder->add(
            'css_id',
            ParameterType\TextLineType::class,
            [
                'translatable' => false,
            ],
        );
    }

    public function getDynamicParameters(DynamicParameters $params, Block $block): void
    {
        $params['definition_param'] = 'definition_value';
    }
}
