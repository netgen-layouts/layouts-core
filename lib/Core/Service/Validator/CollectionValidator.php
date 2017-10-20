<?php

namespace Netgen\BlockManager\Core\Service\Validator;

use Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\ItemCreateStruct;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\Collection\QueryTypeInterface;
use Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct;
use Netgen\BlockManager\Validator\Constraint\Structs\QueryUpdateStruct as QueryUpdateStructConstraint;
use Netgen\BlockManager\Validator\Constraint\ValueType;
use Symfony\Component\Validator\Constraints;

final class CollectionValidator extends Validator
{
    /**
     * Validates the provided collection create struct.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct $collectionCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateCollectionCreateStruct(CollectionCreateStruct $collectionCreateStruct)
    {
        $this->validate(
            $collectionCreateStruct->offset,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'int')),
                new Constraints\GreaterThanOrEqual(array('value' => 0)),
            ),
            'offset'
        );

        if ($collectionCreateStruct->limit !== null) {
            $this->validate(
                $collectionCreateStruct->limit,
                array(
                    new Constraints\Type(array('type' => 'int')),
                    new Constraints\GreaterThan(array('value' => 0)),
                ),
                'limit'
            );
        }

        if ($collectionCreateStruct->queryCreateStruct !== null) {
            $this->validate(
                $collectionCreateStruct->queryCreateStruct,
                array(
                    new Constraints\Type(array('type' => QueryCreateStruct::class)),
                ),
                'queryCreateStruct'
            );

            $this->validateQueryCreateStruct($collectionCreateStruct->queryCreateStruct);
        }
    }

    /**
     * Validates the provided collection update struct.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct $collectionUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateCollectionUpdateStruct(CollectionUpdateStruct $collectionUpdateStruct)
    {
        if ($collectionUpdateStruct->offset !== null) {
            $this->validate(
                $collectionUpdateStruct->offset,
                array(
                    new Constraints\NotBlank(),
                    new Constraints\Type(array('type' => 'int')),
                    new Constraints\GreaterThanOrEqual(array('value' => 0)),
                ),
                'offset'
            );
        }

        if ($collectionUpdateStruct->limit !== null) {
            $this->validate(
                $collectionUpdateStruct->limit,
                array(
                    new Constraints\NotBlank(),
                    new Constraints\Type(array('type' => 'int')),
                    new Constraints\GreaterThanOrEqual(array('value' => 0)),
                ),
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
            $itemCreateStruct->type,
            array(
                new Constraints\NotBlank(),
                new Constraints\Choice(
                    array(
                        'choices' => array(
                            Item::TYPE_MANUAL,
                        ),
                        'strict' => true,
                    )
                ),
            ),
            'type'
        );

        $this->validate(
            $itemCreateStruct->valueId,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'scalar')),
            ),
            'valueId'
        );

        $this->validate(
            $itemCreateStruct->valueType,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'string')),
                new ValueType(),
            ),
            'valueType'
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
            array(
                new Constraints\NotNull(),
                new Constraints\Type(array('type' => QueryTypeInterface::class)),
            ),
            'queryType'
        );

        $this->validate(
            $queryCreateStruct,
            array(
                new ParameterStruct(
                    array(
                        'parameterCollection' => $queryCreateStruct->queryType,
                    )
                ),
            ),
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
            array(
                new QueryUpdateStructConstraint(
                    array(
                        'payload' => $query,
                    )
                ),
            )
        );
    }
}
