<?php

namespace Netgen\BlockManager\Core\Service\Validator;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\ItemCreateStruct;
use Netgen\BlockManager\API\Values\Collection\ItemUpdateStruct;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\Collection\Item\ItemDefinitionInterface;
use Netgen\BlockManager\Collection\QueryTypeInterface;
use Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct;
use Netgen\BlockManager\Validator\Constraint\Structs\QueryUpdateStruct as QueryUpdateStructConstraint;
use Symfony\Component\Validator\Constraints;

final class CollectionValidator extends Validator
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\ConfigValidator
     */
    private $configValidator;

    public function __construct(ConfigValidator $configValidator)
    {
        $this->configValidator = $configValidator;
    }

    /**
     * Validates the provided collection create struct.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct $collectionCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateCollectionCreateStruct(CollectionCreateStruct $collectionCreateStruct)
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
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct $collectionUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateCollectionUpdateStruct(Collection $collection, CollectionUpdateStruct $collectionUpdateStruct)
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
     * @param \Netgen\BlockManager\API\Values\Collection\ItemCreateStruct $itemCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateItemCreateStruct(ItemCreateStruct $itemCreateStruct)
    {
        $this->validate(
            $itemCreateStruct->definition,
            [
                new Constraints\NotNull(),
                new Constraints\Type(['type' => ItemDefinitionInterface::class]),
            ],
            'definition'
        );

        $this->validate(
            $itemCreateStruct->type,
            [
                new Constraints\NotBlank(),
                new Constraints\Choice(
                    [
                        'choices' => [
                            Item::TYPE_MANUAL,
                        ],
                        'strict' => true,
                    ]
                ),
            ],
            'type'
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

        $this->configValidator->validateConfigStructs(
            $itemCreateStruct->getConfigStructs(),
            $itemCreateStruct->definition->getConfigDefinitions()
        );
    }

    /**
     * Validates the provided item update struct.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Item $item
     * @param \Netgen\BlockManager\API\Values\Collection\ItemUpdateStruct $itemUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateItemUpdateStruct(Item $item, ItemUpdateStruct $itemUpdateStruct)
    {
        $this->configValidator->validateConfigStructs(
            $itemUpdateStruct->getConfigStructs(),
            $item->getDefinition()->getConfigDefinitions()
        );
    }

    /**
     * Validates the provided query create struct.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\QueryCreateStruct $queryCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateQueryCreateStruct(QueryCreateStruct $queryCreateStruct)
    {
        $this->validate(
            $queryCreateStruct->queryType,
            [
                new Constraints\NotNull(),
                new Constraints\Type(['type' => QueryTypeInterface::class]),
            ],
            'queryType'
        );

        $this->validate(
            $queryCreateStruct,
            [
                new ParameterStruct(
                    [
                        'parameterCollection' => $queryCreateStruct->queryType,
                    ]
                ),
            ],
            'parameterValues'
        );
    }

    /**
     * Validates the provided query update struct.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param \Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct $queryUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateQueryUpdateStruct(Query $query, QueryUpdateStruct $queryUpdateStruct)
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
}
