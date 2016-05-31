<?php

namespace Netgen\BlockManager\Core\Service\Validator;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\CollectionUpdateStruct;
use Netgen\BlockManager\API\Values\ItemCreateStruct;
use Netgen\BlockManager\API\Values\QueryCreateStruct;
use Netgen\BlockManager\API\Values\QueryUpdateStruct;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface;
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
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the validation failed
     *
     * @return bool
     */
    public function validateCollectionCreateStruct(CollectionCreateStruct $collectionCreateStruct)
    {
        $this->validate(
            $collectionCreateStruct->type,
            array(
                new Constraints\NotBlank(),
                new Constraints\Choice(
                    array(
                        'choices' => array(
                            Collection::TYPE_MANUAL,
                            Collection::TYPE_DYNAMIC,
                            Collection::TYPE_NAMED,
                        ),
                        'strict' => true,
                    )
                ),
            ),
            'type'
        );

        if ($collectionCreateStruct->type === Collection::TYPE_NAMED) {
            $this->validate(
                $collectionCreateStruct->name,
                array(
                    new Constraints\NotBlank(),
                    new Constraints\Type(array('type' => 'string')),
                ),
                'name'
            );
        }

        return true;
    }

    /**
     * Validates collection update struct.
     *
     * @param \Netgen\BlockManager\API\Values\CollectionUpdateStruct $collectionUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the validation failed
     *
     * @return bool
     */
    public function validateCollectionUpdateStruct(CollectionUpdateStruct $collectionUpdateStruct)
    {
        $this->validate(
            $collectionUpdateStruct->name,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'string')),
            ),
            'name'
        );

        return true;
    }

    /**
     * Validates item create struct.
     *
     * @param \Netgen\BlockManager\API\Values\ItemCreateStruct $itemCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the validation failed
     *
     * @return bool
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
                            Item::TYPE_OVERRIDE,
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

        return true;
    }

    /**
     * Validates query create struct.
     *
     * @param \Netgen\BlockManager\API\Values\QueryCreateStruct $queryCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the validation failed
     *
     * @return bool
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
        $fields = $this->buildParameterValidationFields(
            $queryType->getParameters(),
            $queryCreateStruct->getParameters()
        );

        $this->validate(
            $queryCreateStruct->getParameters(),
            array(
                new Constraints\Collection(array('fields' => $fields)),
            ),
            'parameters'
        );

        return true;
    }

    /**
     * Validates query update struct.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param \Netgen\BlockManager\API\Values\QueryUpdateStruct $queryUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the validation failed
     *
     * @return bool
     */
    public function validateQueryUpdateStruct(Query $query, QueryUpdateStruct $queryUpdateStruct)
    {
        if ($queryUpdateStruct->identifier !== null) {
            $this->validate(
                $queryUpdateStruct->identifier,
                array(
                    new Constraints\NotBlank(),
                    new Constraints\Type(array('type' => 'string')),
                ),
                'identifier'
            );
        }

        $queryType = $this->queryTypeRegistry->getQueryType($query->getType());

        $fields = $this->buildParameterValidationFields(
            $queryType->getParameters(),
            $queryUpdateStruct->getParameters(),
            false
        );

        $this->validate(
            $queryUpdateStruct->getParameters(),
            array(
                new Constraints\Collection(array('fields' => $fields)),
            ),
            'parameters'
        );

        return true;
    }
}
