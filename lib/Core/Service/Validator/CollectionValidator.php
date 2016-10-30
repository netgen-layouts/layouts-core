<?php

namespace Netgen\BlockManager\Core\Service\Validator;

use Netgen\BlockManager\Validator\Constraint\Structs\QueryUpdateStruct as QueryUpdateStructConstraint;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\CollectionUpdateStruct;
use Netgen\BlockManager\API\Values\ItemCreateStruct;
use Netgen\BlockManager\API\Values\QueryCreateStruct;
use Netgen\BlockManager\API\Values\QueryUpdateStruct;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface;
use Netgen\BlockManager\Exception\ValidationFailedException;
use Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct;
use Netgen\BlockManager\Validator\Constraint\ValueType;
use Symfony\Component\Validator\Constraints;

class CollectionValidator extends Validator
{
    /**
     * @var \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface
     */
    protected $queryTypeRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface $queryTypeRegistry
     */
    public function __construct(QueryTypeRegistryInterface $queryTypeRegistry)
    {
        $this->queryTypeRegistry = $queryTypeRegistry;
    }

    /**
     * Validates collection create struct.
     *
     * @param \Netgen\BlockManager\API\Values\CollectionCreateStruct $collectionCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\ValidationFailedException If the validation failed
     */
    public function validateCollectionCreateStruct(CollectionCreateStruct $collectionCreateStruct)
    {
        if ($collectionCreateStruct->itemCreateStructs !== null) {
            $this->validate(
                $collectionCreateStruct->itemCreateStructs,
                array(
                    new Constraints\Type(array('type' => 'array')),
                    new Constraints\All(
                        array(
                            'constraints' => array(
                                new Constraints\Type(array('type' => ItemCreateStruct::class)),
                            ),
                        )
                    ),
                ),
                'itemCreateStructs'
            );

            foreach ($collectionCreateStruct->itemCreateStructs as $itemCreateStruct) {
                $this->validateItemCreateStruct($itemCreateStruct);
            }
        }

        if ($collectionCreateStruct->queryCreateStructs !== null) {
            $this->validate(
                $collectionCreateStruct->queryCreateStructs,
                array(
                    new Constraints\Type(array('type' => 'array')),
                    new Constraints\All(
                        array(
                            'constraints' => array(
                                new Constraints\Type(array('type' => QueryCreateStruct::class)),
                            ),
                        )
                    ),
                ),
                'queryCreateStructs'
            );

            foreach ($collectionCreateStruct->queryCreateStructs as $queryCreateStruct) {
                $this->validateQueryCreateStruct($queryCreateStruct);
            }

            $allQueryIdentifiers = array_map(
                function (QueryCreateStruct $queryCreateStruct) {
                    return $queryCreateStruct->identifier;
                },
                $collectionCreateStruct->queryCreateStructs
            );

            if (count($allQueryIdentifiers) !== count(array_unique($allQueryIdentifiers))) {
                throw new ValidationFailedException(
                    'queryCreateStructs',
                    'All query create structs must have a unique identifier.'
                );
            }
        }

        $this->validate(
            $collectionCreateStruct->type,
            array(
                new Constraints\NotBlank(),
                new Constraints\Choice(
                    array(
                        'choices' => array(
                            Collection::TYPE_MANUAL,
                            Collection::TYPE_DYNAMIC,
                        ),
                        'strict' => true,
                    )
                ),
            ),
            'type'
        );

        if ($collectionCreateStruct->shared !== null) {
            $this->validate(
                $collectionCreateStruct->shared,
                array(
                    new Constraints\Type(array('type' => 'bool')),
                ),
                'shared'
            );

            if ($collectionCreateStruct->shared === true) {
                $collectionName = is_string($collectionCreateStruct->name) ?
                    trim($collectionCreateStruct->name) :
                    $collectionCreateStruct->name;

                $this->validate(
                    $collectionName,
                    array(
                        new Constraints\NotBlank(),
                        new Constraints\Type(array('type' => 'string')),
                    ),
                    'name'
                );
            }
        }

        if ($collectionCreateStruct->type === Collection::TYPE_MANUAL) {
            if (
                is_array($collectionCreateStruct->queryCreateStructs) &&
                !empty($collectionCreateStruct->queryCreateStructs)
            ) {
                throw new ValidationFailedException(
                    'queryCreateStructs',
                    'Manual collection cannot have any queries.'
                );
            }
        } elseif ($collectionCreateStruct->type === Collection::TYPE_DYNAMIC) {
            if (
                !is_array($collectionCreateStruct->queryCreateStructs) ||
                empty($collectionCreateStruct->queryCreateStructs)
            ) {
                throw new ValidationFailedException(
                    'queryCreateStructs',
                    'Dynamic collection needs to have at least one query.'
                );
            }
        }
    }

    /**
     * Validates collection update struct.
     *
     * @param \Netgen\BlockManager\API\Values\CollectionUpdateStruct $collectionUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\ValidationFailedException If the validation failed
     */
    public function validateCollectionUpdateStruct(CollectionUpdateStruct $collectionUpdateStruct)
    {
        $collectionName = is_string($collectionUpdateStruct->name) ?
            trim($collectionUpdateStruct->name) :
            $collectionUpdateStruct->name;

        $this->validate(
            $collectionName,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'string')),
            ),
            'name'
        );
    }

    /**
     * Validates item create struct.
     *
     * @param \Netgen\BlockManager\API\Values\ItemCreateStruct $itemCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\ValidationFailedException If the validation failed
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
     * Validates query create struct.
     *
     * @param \Netgen\BlockManager\API\Values\QueryCreateStruct $queryCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\ValidationFailedException If the validation failed
     */
    public function validateQueryCreateStruct(QueryCreateStruct $queryCreateStruct)
    {
        $this->validate(
            $queryCreateStruct->identifier,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'string')),
            ),
            'identifier'
        );

        $this->validate(
            $queryCreateStruct->type,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'string')),
            ),
            'type'
        );

        $queryType = $this->queryTypeRegistry->getQueryType($queryCreateStruct->type);

        $this->validate(
            $queryCreateStruct,
            array(
                new ParameterStruct(
                    array(
                        'parameters' => $queryType->getParameters(),
                        'required' => true,
                    )
                ),
            ),
            'parameters'
        );
    }

    /**
     * Validates query update struct.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param \Netgen\BlockManager\API\Values\QueryUpdateStruct $queryUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\ValidationFailedException If the validation failed
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
