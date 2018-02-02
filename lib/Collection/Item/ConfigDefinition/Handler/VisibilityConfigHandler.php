<?php

namespace Netgen\BlockManager\Collection\Item\ConfigDefinition\Handler;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

/**
 * This handler specifies the model of visibility configuration within
 * the collection items.
 */
final class VisibilityConfigHandler implements ConfigDefinitionHandlerInterface
{
    public function buildParameters(ParameterBuilderInterface $builder)
    {
        $builder->add(
            'visible',
            ParameterType\Compound\BooleanType::class,
            array(
                'default_value' => true,
            )
        );

        $builder->get('visible')->add(
            'visible_from',
            ParameterType\DateTimeType::class
        );

        $builder->get('visible')->add(
            'visible_to',
            ParameterType\DateTimeType::class
        );
    }

    public function isEnabled(ConfigAwareValue $configAwareValue)
    {
        if (!$configAwareValue instanceof Item) {
            return false;
        }

        return true;
    }
}
