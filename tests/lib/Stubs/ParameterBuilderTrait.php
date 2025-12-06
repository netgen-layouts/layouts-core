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
                    new ParameterType\BooleanType(),
                    new ParameterType\TextLineType(),
                    new ParameterType\IntegerType(),
                    new ParameterType\Compound\BooleanType(),
                ],
            ),
        );

        $builder = $builderFactory->createParameterBuilder();
        $this->buildParameters($builder);

        return $builder->buildParameterDefinitions();
    }
}
