<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Config\Stubs\Block;

use Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;

final class ConfigHandler implements ConfigDefinitionHandlerInterface
{
    public function getParameterDefinitions(): array
    {
        return [
            'param1' => new ParameterDefinition(
                [
                    'name' => 'param1',
                    'type' => new ParameterType\BooleanType(),
                ]
            ),
            'param2' => new ParameterDefinition(
                [
                    'name' => 'param2',
                    'type' => new ParameterType\IntegerType(),
                ]
            ),
        ];
    }

    public function buildParameters(ParameterBuilderInterface $builder): void
    {
        $builder->add('param1', ParameterType\BooleanType::class);
        $builder->add('param2', ParameterType\IntegerType::class);
    }
}
