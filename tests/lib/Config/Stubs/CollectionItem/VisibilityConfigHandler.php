<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Config\Stubs\CollectionItem;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;

final class VisibilityConfigHandler implements ConfigDefinitionHandlerInterface
{
    public function getParameterDefinitions()
    {
        return [
            'visibility_status' => new ParameterDefinition(
                [
                    'name' => 'visibility_status',
                    'type' => new ParameterType\ChoiceType(),
                    'options' => [
                        'multiple' => false,
                        'options' => [
                            Item::VISIBILITY_VISIBLE => Item::VISIBILITY_VISIBLE,
                            Item::VISIBILITY_HIDDEN => Item::VISIBILITY_HIDDEN,
                            Item::VISIBILITY_SCHEDULED => Item::VISIBILITY_SCHEDULED,
                        ],
                    ],
                ]
            ),
            'visible_from' => new ParameterDefinition(
                [
                    'name' => 'visible_from',
                    'type' => new ParameterType\DateTimeType(),
                ]
            ),
            'visible_to' => new ParameterDefinition(
                [
                    'name' => 'visible_to',
                    'type' => new ParameterType\DateTimeType(),
                ]
            ),
        ];
    }

    public function buildParameters(ParameterBuilderInterface $builder)
    {
    }
}
