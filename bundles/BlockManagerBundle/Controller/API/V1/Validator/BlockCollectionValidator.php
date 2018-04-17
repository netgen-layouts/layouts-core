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
            [
                new Constraints\Type(['type' => 'array']),
                new Constraints\NotBlank(),
                new Constraints\All(
                    [
                        'constraints' => new Constraints\Collection(
                            [
                                'fields' => [
                                    'type' => [
                                        new Constraints\NotNull(),
                                        new Constraints\Type(['type' => 'int']),
                                    ],
                                    'value' => [
                                        new Constraints\NotNull(),
                                        new Constraints\Type(['type' => 'scalar']),
                                    ],
                                    'value_type' => [
                                        new Constraints\NotBlank(),
                                        new Constraints\Type(['type' => 'string']),
                                    ],
                                    'position' => new Constraints\Optional(
                                        [
                                            new Constraints\NotNull(),
                                            new Constraints\Type(['type' => 'int']),
                                        ]
                                    ),
                                ],
                            ]
                        ),
                    ]
                ),
            ],
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
            [
                new Constraints\NotBlank(),
                new Constraints\Choice(
                    [
                        'choices' => [
                            Collection::TYPE_MANUAL,
                            Collection::TYPE_DYNAMIC,
                        ],
                        'strict' => true,
                    ]
                ),
            ],
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
            if ($collectionConfig->getValidItemTypes() === []) {
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
            return;
        }

        return $blockDefinition->getCollection($collectionIdentifier);
    }
}
