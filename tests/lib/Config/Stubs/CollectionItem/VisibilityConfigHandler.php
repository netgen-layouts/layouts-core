<?php

namespace Netgen\BlockManager\Tests\Config\Stubs\CollectionItem;

use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinitionHandler;
use Netgen\BlockManager\Tests\Parameters\Stubs\CompoundParameterDefinition;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterDefinition;

final class VisibilityConfigHandler extends ConfigDefinitionHandler
{
    public function getParameterDefinitions()
    {
        return array(
            'visible' => new CompoundParameterDefinition(
                array(
                    'name' => 'visible',
                    'type' => new ParameterType\Compound\BooleanType(),
                    'defaultValue' => true,
                    'parameterDefinitions' => array(
                        'visible_from' => new ParameterDefinition(
                            array(
                                'name' => 'visible_from',
                                'type' => new ParameterType\DateTimeType(),
                            )
                        ),
                        'visible_to' => new ParameterDefinition(
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
