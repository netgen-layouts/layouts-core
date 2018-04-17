<?php

namespace Netgen\BlockManager\Core\Service\Validator;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Validator\Constraint\BlockItemViewType;
use Netgen\BlockManager\Validator\Constraint\BlockViewType;
use Netgen\BlockManager\Validator\Constraint\Structs\BlockUpdateStruct as BlockUpdateStructConstraint;
use Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct;
use Symfony\Component\Validator\Constraints;

final class BlockValidator extends Validator
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\ConfigValidator
     */
    private $configValidator;

    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\CollectionValidator
     */
    private $collectionValidator;

    public function __construct(ConfigValidator $configValidator, CollectionValidator $collectionValidator)
    {
        $this->configValidator = $configValidator;
        $this->collectionValidator = $collectionValidator;
    }

    /**
     * Validates the provided block create struct.
     *
     * @param \Netgen\BlockManager\API\Values\Block\BlockCreateStruct $blockCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateBlockCreateStruct(BlockCreateStruct $blockCreateStruct)
    {
        $this->validate(
            $blockCreateStruct->definition,
            [
                new Constraints\NotNull(),
                new Constraints\Type(['type' => BlockDefinitionInterface::class]),
            ],
            'definition'
        );

        $this->validate(
            $blockCreateStruct->viewType,
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'string']),
                new BlockViewType(['definition' => $blockCreateStruct->definition]),
            ],
            'viewType'
        );

        $this->validate(
            $blockCreateStruct->itemViewType,
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'string']),
                new BlockItemViewType(
                    [
                        'viewType' => $blockCreateStruct->viewType,
                        'definition' => $blockCreateStruct->definition,
                    ]
                ),
            ],
            'itemViewType'
        );

        if ($blockCreateStruct->name !== null) {
            $this->validate(
                $blockCreateStruct->name,
                [
                    new Constraints\Type(['type' => 'string']),
                ],
                'name'
            );
        }

        $this->validate(
            $blockCreateStruct->isTranslatable,
            [
                new Constraints\NotNull(),
                new Constraints\Type(['type' => 'bool']),
            ],
            'isTranslatable'
        );

        $this->validate(
            $blockCreateStruct->alwaysAvailable,
            [
                new Constraints\NotNull(),
                new Constraints\Type(['type' => 'bool']),
            ],
            'alwaysAvailable'
        );

        $this->validate(
            $blockCreateStruct,
            [
                new ParameterStruct(
                    [
                        'parameterCollection' => $blockCreateStruct->definition,
                    ]
                ),
            ],
            'parameterValues'
        );

        $this->configValidator->validateConfigStructs(
            $blockCreateStruct->getConfigStructs(),
            $blockCreateStruct->definition->getConfigDefinitions()
        );

        $collectionCreateStructs = $blockCreateStruct->getCollectionCreateStructs();
        if (!empty($collectionCreateStructs)) {
            foreach ($collectionCreateStructs as $collectionCreateStruct) {
                $this->collectionValidator->validateCollectionCreateStruct($collectionCreateStruct);
            }
        }
    }

    /**
     * Validates the provided block update struct.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Netgen\BlockManager\API\Values\Block\BlockUpdateStruct $blockUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateBlockUpdateStruct(Block $block, BlockUpdateStruct $blockUpdateStruct)
    {
        $this->validate(
            $blockUpdateStruct,
            [
                new BlockUpdateStructConstraint(
                    [
                        'payload' => $block,
                    ]
                ),
            ]
        );

        $this->configValidator->validateConfigStructs(
            $blockUpdateStruct->getConfigStructs(),
            $block->getDefinition()->getConfigDefinitions()
        );
    }
}
