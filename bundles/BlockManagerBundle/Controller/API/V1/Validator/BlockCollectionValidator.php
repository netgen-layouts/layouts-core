<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator;

use Netgen\BlockManager\API\Values\Block\CollectionReference;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Exception\Validation\ValidationException;
use Symfony\Component\Validator\Constraints;

class BlockCollectionValidator extends Validator
{
    /**
     * Validates item creation parameters from the request.
     *
     * @param \Netgen\BlockManager\API\Values\Block\CollectionReference $collectionReference
     * @param array $items
     */
    public function validateAddItems(CollectionReference $collectionReference, $items)
    {
        $this->validate(
            $items,
            array(
                new Constraints\Type(array('type' => 'array')),
                new Constraints\NotBlank(),
                new Constraints\All(
                    array(
                        'constraints' => new Constraints\Collection(
                            array(
                                'fields' => array(
                                    'type' => array(
                                        new Constraints\NotNull(),
                                        new Constraints\Type(array('type' => 'int')),
                                    ),
                                    'value_id' => array(
                                        new Constraints\NotNull(),
                                        new Constraints\Type(array('type' => 'scalar')),
                                    ),
                                    'value_type' => array(
                                        new Constraints\NotBlank(),
                                        new Constraints\Type(array('type' => 'string')),
                                    ),
                                    'position' => new Constraints\Optional(
                                        array(
                                            new Constraints\NotNull(),
                                            new Constraints\Type(array('type' => 'int')),
                                        )
                                    ),
                                ),
                            )
                        ),
                    )
                ),
            ),
            'items'
        );

        $collectionIdentifier = $collectionReference->getIdentifier();
        $blockDefinition = $collectionReference->getBlock()->getDefinition();

        if ($blockDefinition->getConfig()->hasCollection($collectionIdentifier)) {
            $collectionConfig = $blockDefinition->getConfig()->getCollection($collectionIdentifier);

            foreach ($items as $item) {
                if (!$collectionConfig->isValidItemType($item['value_type'])) {
                    throw ValidationException::validationFailed(
                        'value_type',
                        sprintf(
                            'Value type "%s" is not allowed in selected block.',
                            $item['value_type']
                        )
                    );
                }
            }
        }
    }

    /**
     * Validates block creation parameters from the request.
     *
     * @param \Netgen\BlockManager\API\Values\Block\CollectionReference $collectionReference
     * @param int $newType
     * @param string $queryType
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If validation failed
     */
    public function validateChangeCollectionType(CollectionReference $collectionReference, $newType, $queryType)
    {
        $this->validate(
            $newType,
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
            'new_type'
        );

        if ($newType === Collection::TYPE_DYNAMIC) {
            $blockDefinition = $collectionReference->getBlock()->getDefinition();

            $collectionIdentifier = $collectionReference->getIdentifier();
            if ($blockDefinition->getConfig()->hasCollection($collectionIdentifier)) {
                $collectionConfig = $blockDefinition->getConfig()->getCollection($collectionIdentifier);

                if (!$collectionConfig->isValidQueryType($queryType)) {
                    throw ValidationException::validationFailed(
                        'query_type',
                        sprintf(
                            'Query type "%s" is not allowed in selected block.',
                            $queryType
                        )
                    );
                }
            }
        }
    }
}
