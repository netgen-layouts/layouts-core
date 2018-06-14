<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Collection\Item\ConfigDefinition\Handler;

use DateTimeInterface;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;

/**
 * This handler specifies the model of visibility configuration within
 * the collection items.
 */
final class VisibilityConfigHandler implements ConfigDefinitionHandlerInterface
{
    public function buildParameters(ParameterBuilderInterface $builder): void
    {
        $builder->add(
            'visibility_status',
            ParameterType\ChoiceType::class,
            [
                'expanded' => true,
                'options' => [
                    'Always visible' => Item::VISIBILITY_VISIBLE,
                    'Always hidden' => Item::VISIBILITY_HIDDEN,
                    'Scheduled visibility' => Item::VISIBILITY_SCHEDULED,
                ],
                'default_value' => Item::VISIBILITY_VISIBLE,
            ]
        );

        $builder->add(
            'visible_from',
            ParameterType\DateTimeType::class
        );

        $builder->add(
            'visible_to',
            ParameterType\DateTimeType::class,
            [
                'constraints' => [
                    function ($visibleTo, array $parameters): ?Constraint {
                        $visibleFrom = $parameters['visible_from'];

                        if (
                            !$visibleFrom instanceof DateTimeInterface ||
                            !$visibleTo instanceof DateTimeInterface
                        ) {
                            return null;
                        }

                        return new Constraints\GreaterThan(
                            [
                                'value' => $visibleFrom,
                                'message' => 'netgen_block_manager.config.item.visibility.visible_to',
                            ]
                        );
                    },
                ],
            ]
        );
    }
}
