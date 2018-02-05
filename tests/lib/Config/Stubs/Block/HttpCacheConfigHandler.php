<?php

namespace Netgen\BlockManager\Tests\Config\Stubs\Block;

use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinitionHandler;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterDefinition;

final class HttpCacheConfigHandler extends ConfigDefinitionHandler
{
    /**
     * Returns the array specifying block parameter definitions.
     *
     * The keys are parameter identifiers.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[]
     */
    public function getParameterDefinitions()
    {
        return array(
            'use_http_cache' => new ParameterDefinition(
                array(
                    'name' => 'use_http_cache',
                    'type' => new ParameterType\BooleanType(),
                )
            ),
            'shared_max_age' => new ParameterDefinition(
                array(
                    'name' => 'shared_max_age',
                    'type' => new ParameterType\IntegerType(),
                )
            ),
        );
    }
}
