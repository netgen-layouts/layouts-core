<?php

declare(strict_types=1);

namespace Netgen\Layouts\Core\Validator;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\BlockCreateStruct;
use Netgen\Layouts\API\Values\Block\BlockUpdateStruct;
use Netgen\Layouts\Validator\Constraint\Structs\BlockCreateStruct as BlockCreateStructConstraint;
use Netgen\Layouts\Validator\Constraint\Structs\BlockUpdateStruct as BlockUpdateStructConstraint;
use Netgen\Layouts\Validator\Constraint\Structs\ConfigAwareStruct as ConfigAwareStructConstraint;
use Netgen\Layouts\Validator\ValidatorTrait;

use function count;

final class BlockValidator
{
    use ValidatorTrait;

    private CollectionValidator $collectionValidator;

    public function __construct(CollectionValidator $collectionValidator)
    {
        $this->collectionValidator = $collectionValidator;
    }

    /**
     * Validates the provided block create struct.
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If the validation failed
     */
    public function validateBlockCreateStruct(BlockCreateStruct $blockCreateStruct): void
    {
        $this->validate(
            $blockCreateStruct,
            [
                new BlockCreateStructConstraint(),
            ],
        );

        $this->validate(
            $blockCreateStruct,
            new ConfigAwareStructConstraint(
                [
                    'payload' => $blockCreateStruct->getDefinition(),
                ],
            ),
        );

        $collectionCreateStructs = $blockCreateStruct->getCollectionCreateStructs();
        if (count($collectionCreateStructs) > 0) {
            foreach ($collectionCreateStructs as $collectionCreateStruct) {
                $this->collectionValidator->validateCollectionCreateStruct($collectionCreateStruct);
            }
        }
    }

    /**
     * Validates the provided block update struct.
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If the validation failed
     */
    public function validateBlockUpdateStruct(Block $block, BlockUpdateStruct $blockUpdateStruct): void
    {
        $this->validate(
            $blockUpdateStruct,
            [
                new BlockUpdateStructConstraint(
                    [
                        'payload' => $block,
                    ],
                ),
            ],
        );

        $this->validate(
            $blockUpdateStruct,
            new ConfigAwareStructConstraint(
                [
                    'payload' => $block->getDefinition(),
                    'allowMissingFields' => true,
                ],
            ),
        );
    }
}
