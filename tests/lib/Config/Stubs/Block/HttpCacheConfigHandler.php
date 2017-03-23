<?php

namespace Netgen\BlockManager\Tests\Config\Stubs\Block;

use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinitionHandler;
use Netgen\BlockManager\Tests\Parameters\Stubs\CompoundParameter;
use Netgen\BlockManager\Tests\Parameters\Stubs\Parameter;

class HttpCacheConfigHandler extends ConfigDefinitionHandler
{
    /**
     * Returns the array specifying block parameters.
     *
     * The keys are parameter identifiers.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        $useHttpCacheParam = new CompoundParameter(
            array(
                'name' => 'use_http_cache',
                'type' => new ParameterType\Compound\BooleanType(),
            )
        );

        $useHttpCacheParam->setParameters(
            array(
                'shared_max_age' => new Parameter(
                    array(
                        'name' => 'shared_max_age',
                        'type' => new ParameterType\IntegerType(),
                    )
                ),
            )
        );

        return array(
            'use_http_cache' => $useHttpCacheParam,
        );
    }
}
