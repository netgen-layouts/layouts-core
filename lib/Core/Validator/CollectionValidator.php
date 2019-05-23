<?php

declare(strict_types=1);

namespace Netgen\Layouts\Core\Validator;

use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\CollectionCreateStruct;
use Netgen\Layouts\API\Values\Collection\CollectionUpdateStruct;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\API\Values\Collection\ItemCreateStruct;
use Netgen\Layouts\API\Values\Collection\ItemUpdateStruct;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\API\Values\Collection\QueryCreateStruct;
use Netgen\Layouts\API\Values\Collection\QueryUpdateStruct;
use Netgen\Layouts\API\Values\Collection\SlotCreateStruct;
use Netgen\Layouts\API\Values\Collection\SlotUpdateStruct;
use Netgen\Layouts\Collection\Item\ItemDefinitionInterface;
use Netgen\Layouts\Validator\Constraint\Structs\ConfigAwareStruct as ConfigAwareStructConstraint;
use Netgen\Layouts\Validator\Constraint\Structs\ParameterStruct;
use Netgen\Layouts\Validator\Constraint\Structs\QueryUpdateStruct as QueryUpdateStructConstraint;
use Symfony\Component\Validator\Constraints;

final class CollectionValidator extends Validator
{
    /**
     * Validates the provided collection create struct.
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If the validation failed
     */
    public function validateCollectionCreateStruct(CollectionCreateStruct $collectionCreateStruct): void
    {
        if ($collectionCreateStruct->queryCreateStruct !== null) {
            $this->validate(
                $collectionCreateStruct->queryCreateStruct,
                [
                    new Constraints\Type(['type' => QueryCreateStruct::class]),
                ],
                'queryCreateStruct'
            );

            $this->validateQueryCreateStruct($collectionCreateStruct->queryCreateStruct);
        }

        $offsetConstraints = [
            new Constraints\NotBlank(),
            new Constraints\Type(['type' => 'int']),
        ];

        $offsetConstraints[] = $collectionCreateStruct->queryCreateStruct !== null ?
            new Constraints\GreaterThanOrEqual(['value' => 0]) :
            new Constraints\EqualTo(['value' => 0]);

        $this->validate(
            $collectionCreateStruct->offset,
            $offsetConstraints,
            'offset'
        );

        if ($collectionCreateStruct->limit !== null) {
            $this->validate(
                $collectionCreateStruct->limit,
                [
                    new Constraints\Type(['type' => 'int']),
                    new Constraints\GreaterThan(['value' => 0]),
                ],
                'limit'
            );
        }
    }

    /**
     * Validates the provided collection update struct.
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If the validation failed
     */
    public function validateCollectionUpdateStruct(Collection $collection, CollectionUpdateStruct $collectionUpdateStruct): void
    {
        if ($collectionUpdateStruct->offset !== null) {
            $offsetConstraints = [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'int']),
            ];

            $offsetConstraints[] = $collection->hasQuery() ?
                new Constraints\GreaterThanOrEqual(['value' => 0]) :
                new Constraints\EqualTo(['value' => 0]);

            $this->validate(
                $collectionUpdateStruct->offset,
                $offsetConstraints,
                'offset'
            );
        }

        if ($collectionUpdateStruct->limit !== null) {
            $this->validate(
                $collectionUpdateStruct->limit,
                [
                    new Constraints\NotBlank(),
                    new Constraints\Type(['type' => 'int']),
                    new Constraints\GreaterThanOrEqual(['value' => 0]),
                ],
                'limit'
            );
        }
    }

    /**
     * Validates the provided item create struct.
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If the validation failed
     */
    public function validateItemCreateStruct(ItemCreateStruct $itemCreateStruct): void
    {
        $this->validate(
            $itemCreateStruct->definition,
            [
                new Constraints\NotNull(),
                new Constraints\Type(['type' => ItemDefinitionInterface::class]),
            ],
            'definition'
        );

        if ($itemCreateStruct->value !== null) {
            $this->validate(
                $itemCreateStruct->value,
                [
                    new Constraints\Type(['type' => 'scalar']),
                ],
                'value'
            );
        }

        $this->validate(
            $itemCreateStruct,
            new ConfigAwareStructConstraint(
                [
                    'payload' => $itemCreateStruct->definition,
                ]
            )
        );
    }

    /**
     * Validates the provided item update struct.
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If the validation failed
     */
    public function validateItemUpdateStruct(Item $item, ItemUpdateStruct $itemUpdateStruct): void
    {
        $this->validate(
            $itemUpdateStruct,
            new ConfigAwareStructConstraint(
                [
                    'payload' => $item->getDefinition(),
                    'allowMissingFields' => true,
                ]
            )
        );
    }

    /**
     * Validates the provided query create struct.
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If the validation failed
     */
    public function validateQueryCreateStruct(QueryCreateStruct $queryCreateStruct): void
    {
        $this->validate(
            $queryCreateStruct,
            [
                new ParameterStruct(
                    [
                        'parameterDefinitions' => $queryCreateStruct->getQueryType(),
                    ]
                ),
            ],
            'parameterValues'
        );
    }

    /**
     * Validates the provided query update struct.
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If the validation failed
     */
    public function validateQueryUpdateStruct(Query $query, QueryUpdateStruct $queryUpdateStruct): void
    {
        $this->validate(
            $queryUpdateStruct,
            [
                new QueryUpdateStructConstraint(
                    [
                        'payload' => $query,
                    ]
                ),
            ]
        );
    }

    /**
     * Validates the provided slot create struct.
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If the validation failed
     */
    public function validateSlotCreateStruct(SlotCreateStruct $slotCreateStruct): void
    {
        $this->validate(
            $slotCreateStruct->viewType,
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'string']),
            ],
            'viewType'
        );
    }

    /**
     * Validates the provided slot update struct.
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If the validation failed
     */
    public function validateSlotUpdateStruct(SlotUpdateStruct $slotUpdateStruct): void
    {
        if ($slotUpdateStruct->viewType !== null) {
            $this->validate(
                $slotUpdateStruct->viewType,
                [
                    new Constraints\NotBlank(),
                    new Constraints\Type(['type' => 'string']),
                ],
                'viewType'
            );
        }
    }
}
