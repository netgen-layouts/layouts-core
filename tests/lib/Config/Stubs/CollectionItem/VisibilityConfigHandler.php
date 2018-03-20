<?php

namespace Netgen\BlockManager\Tests\Config\Stubs\CollectionItem;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinitionHandler;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterDefinition;

final class VisibilityConfigHandler extends ConfigDefinitionHandler
{
    public function getParameterDefinitions()
    {
        return array(
            'visibility_status' => new ParameterDefinition(
                array(
                    'name' => 'visibility_status',
                    'type' => new ParameterType\ChoiceType(),
                    'options' => array(
                        'options' => array(
                            Item::VISIBILITY_VISIBLE => Item::VISIBILITY_VISIBLE,
                            Item::VISIBILITY_HIDDEN => Item::VISIBILITY_HIDDEN,
                            Item::VISIBILITY_SCHEDULED => Item::VISIBILITY_SCHEDULED,
                        ),
                    ),
                )
            ),
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
        );
    }
}
