<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Exception\Validation\ValidationException;
use Netgen\Bundle\BlockManagerBundle\Controller\Validator\Validator;
use Symfony\Component\Validator\Constraints;

final class BlockCollectionValidator extends Validator
{
    /**
     * Validates item creation parameters from the request.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param string $collectionIdentifier
     * @param array $items
     */
    public function validateAddItems(Block $block, $collectionIdentifier, $items)
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

        $collectionConfig = $this->getCollectionConfig($block, $collectionIdentifier);
        if ($collectionConfig === null) {
            return;
        }

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

    /**
     * Validates block creation parameters from the request.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param string $collectionIdentifier
     * @param int $newType
     * @param string $queryType
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If validation failed
     */
    public function validateChangeCollectionType(Block $block, $collectionIdentifier, $newType, $queryType)
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

        $collectionConfig = $this->getCollectionConfig($block, $collectionIdentifier);
        if ($collectionConfig === null) {
            return;
        }

        if ($newType === Collection::TYPE_DYNAMIC) {
            if (!$collectionConfig->isValidQueryType($queryType)) {
                throw ValidationException::validationFailed(
                    'new_type',
                    sprintf(
                        'Query type "%s" is not allowed in selected block.',
                        $queryType
                    )
                );
            }
        } elseif ($newType === Collection::TYPE_MANUAL) {
            if ($collectionConfig->getValidItemTypes() === array()) {
                throw ValidationException::validationFailed(
                    'new_type',
                    'Selected block does not allow manual collections.'
                );
            }
        }
    }

    /**
     * Returns the block collection configuration.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param string $collectionIdentifier
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection|null
     */
    private function getCollectionConfig(Block $block, $collectionIdentifier)
    {
        $blockDefinition = $block->getDefinition();

        if (!$blockDefinition->hasCollection($collectionIdentifier)) {
            return null;
        }

        return $blockDefinition->getCollection($collectionIdentifier);
    }
}
