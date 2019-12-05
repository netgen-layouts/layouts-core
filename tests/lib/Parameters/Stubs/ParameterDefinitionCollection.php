<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Stubs;

use Netgen\Layouts\Parameters\ParameterDefinitionCollectionInterface;
use Netgen\Layouts\Parameters\ParameterDefinitionCollectionTrait;

final class ParameterDefinitionCollection implements ParameterDefinitionCollectionInterface
{
    use ParameterDefinitionCollectionTrait;

    /**
     * @param \Netgen\Layouts\Parameters\ParameterDefinition[] $parameterDefinitions
     */
    public function __construct(array $parameterDefinitions = [])
    {
        $this->parameterDefinitions = $parameterDefinitions;
    }
}
