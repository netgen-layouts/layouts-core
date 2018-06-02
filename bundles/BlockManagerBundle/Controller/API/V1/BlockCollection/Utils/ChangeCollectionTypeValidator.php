<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockCollection\Utils;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Exception\Validation\ValidationException;
use Netgen\BlockManager\Validator\ValidatorTrait;
use Symfony\Component\Validator\Constraints;

final class ChangeCollectionTypeValidator
{
    use ValidatorTrait;

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

        $blockDefinition = $block->getDefinition();
        if (!$blockDefinition->hasCollection($collectionIdentifier)) {
            return;
        }

        $collectionConfig = $blockDefinition->getCollection($collectionIdentifier);

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
}
