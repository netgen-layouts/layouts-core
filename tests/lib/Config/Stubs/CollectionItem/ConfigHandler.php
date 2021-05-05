<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Config\Stubs\CollectionItem;

use Netgen\Layouts\Config\ConfigDefinitionHandlerInterface;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType;

final class ConfigHandler implements ConfigDefinitionHandlerInterface
{
    /**
     * @return array<string, \Netgen\Layouts\Parameters\ParameterDefinition>
     */
    public function getParameterDefinitions(): array
    {
        return [
            'param1' => ParameterDefinition::fromArray(
                [
                    'name' => 'param1',
                    'type' => new ParameterType\BooleanType(),
                    'isRequired' => false,
                ],
            ),
            'param2' => ParameterDefinition::fromArray(
                [
                    'name' => 'param2',
                    'type' => new ParameterType\IntegerType(),
                    'isRequired' => false,
                ],
            ),
        ];
    }

    public function buildParameters(ParameterBuilderInterface $builder): void
    {
        $builder->add('param1', ParameterType\BooleanType::class);
        $builder->add('param2', ParameterType\IntegerType::class);
    }
}
