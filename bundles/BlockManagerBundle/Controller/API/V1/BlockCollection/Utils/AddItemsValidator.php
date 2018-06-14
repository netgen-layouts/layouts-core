<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockCollection\Utils;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Exception\Validation\ValidationException;
use Netgen\BlockManager\Validator\ValidatorTrait;
use Symfony\Component\Validator\Constraints;

final class AddItemsValidator
{
    use ValidatorTrait;

    /**
     * Validates item creation parameters from the request.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param string $collectionIdentifier
     * @param array $items
     */
    public function validateAddItems(Block $block, string $collectionIdentifier, $items): void
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

        $blockDefinition = $block->getDefinition();
        if (!$blockDefinition->hasCollection($collectionIdentifier)) {
            return;
        }

        $collectionConfig = $blockDefinition->getCollection($collectionIdentifier);

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
