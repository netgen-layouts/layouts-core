<?php

namespace Netgen\BlockManager\Tests\Config\Stubs\CollectionItem;

use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinitionHandler;
use Netgen\BlockManager\Tests\Parameters\Stubs\CompoundParameter;
use Netgen\BlockManager\Tests\Parameters\Stubs\Parameter;

final class VisibilityConfigHandler extends ConfigDefinitionHandler
{
    public function getParameters()
    {
        return array(
            'visible' => new CompoundParameter(
                array(
                    'name' => 'visible',
                    'type' => new ParameterType\Compound\BooleanType(),
                    'parameters' => array(
                        'visible_from' => new Parameter(
                            array(
                                'name' => 'visible_from',
                                'type' => new ParameterType\DateTimeType(),
                            )
                        ),
                        'visible_to' => new Parameter(
                            array(
                                'name' => 'visible_to',
                                'type' => new ParameterType\DateTimeType(),
                            )
                        ),
                    ),
                )
            ),
        );
    }
}
