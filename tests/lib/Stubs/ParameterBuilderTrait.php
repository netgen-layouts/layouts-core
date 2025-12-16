<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Stubs;

use Netgen\Layouts\Parameters\ParameterBuilderFactory;
use Netgen\Layouts\Parameters\ParameterType;
use Netgen\Layouts\Parameters\Registry\ParameterTypeRegistry;

trait ParameterBuilderTrait
{
    /**
     * @return array<string, \Netgen\Layouts\Parameters\ParameterDefinition>
     */
    public function getParameterDefinitions(): array
    {
        $builderFactory = new ParameterBuilderFactory(
            new ParameterTypeRegistry(
                [
                    ParameterType\BooleanType::getIdentifier() => new ParameterType\BooleanType(),
                    ParameterType\TextLineType::getIdentifier() => new ParameterType\TextLineType(),
                    ParameterType\IntegerType::getIdentifier() => new ParameterType\IntegerType(),
                    ParameterType\Compound\BooleanType::getIdentifier() => new ParameterType\Compound\BooleanType(),
                ],
            ),
        );

        $builder = $builderFactory->createParameterBuilder();
        $this->buildParameters($builder);

        return $builder->buildParameterDefinitions();
    }
}
