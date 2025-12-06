<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Config\Stubs;

use Netgen\Layouts\Config\ConfigDefinitionHandlerInterface;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use Netgen\Layouts\Parameters\ParameterType;
use Netgen\Layouts\Tests\Stubs\ParameterBuilderTrait;

final class ConfigDefinitionHandler implements ConfigDefinitionHandlerInterface
{
    use ParameterBuilderTrait;

    public function buildParameters(ParameterBuilderInterface $builder): void
    {
        $builder->add('param', ParameterType\TextLineType::class);
        $builder->add('param2', ParameterType\TextLineType::class);
    }
}
