<?php

namespace Netgen\BlockManager\Core\Service\Validator;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\API\Values\Page\BlockCreateStruct;
use Netgen\BlockManager\API\Values\Page\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Page\PlaceholderCreateStruct;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Block\PlaceholderDefinitionInterface;
use Netgen\BlockManager\Validator\Constraint\BlockItemViewType;
use Netgen\BlockManager\Validator\Constraint\BlockViewType;
use Netgen\BlockManager\Validator\Constraint\Structs\BlockUpdateStruct as BlockUpdateStructConstraint;
use Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct;
use Symfony\Component\Validator\Constraints;

class BlockValidator extends Validator
{
    /**
     * Validates block create struct.
     *
     * @param \Netgen\BlockManager\API\Values\Page\BlockCreateStruct $blockCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\ValidationFailedException If the validation failed
     */
    public function validateBlockCreateStruct(BlockCreateStruct $blockCreateStruct)
    {
        $this->validate(
            $blockCreateStruct->definition,
            array(
                new Constraints\NotNull(),
                new Constraints\Type(array('type' => BlockDefinitionInterface::class)),
            ),
            'definition'
        );

        $this->validate(
            $blockCreateStruct->viewType,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'string')),
                new BlockViewType(array('definition' => $blockCreateStruct->definition)),
            ),
            'viewType'
        );

        $this->validate(
            $blockCreateStruct->itemViewType,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'string')),
                new BlockItemViewType(
                    array(
                        'viewType' => $blockCreateStruct->viewType,
                        'definition' => $blockCreateStruct->definition,
                    )
                ),
            ),
            'itemViewType'
        );

        if ($blockCreateStruct->name !== null) {
            $this->validate(
                $blockCreateStruct->name,
                array(
                    new Constraints\Type(array('type' => 'string')),
                ),
                'name'
            );
        }

        $this->validate(
            $blockCreateStruct,
            array(
                new ParameterStruct(
                    array(
                        'parameterCollection' => $blockCreateStruct->definition,
                    )
                ),
            ),
            'parameterValues'
        );

        if ($blockCreateStruct->definition->isContainer()) {
            foreach ($blockCreateStruct->definition->getPlaceholders() as $placeholderDefinition) {
                if (!$blockCreateStruct->hasPlaceholderStruct($placeholderDefinition->getIdentifier())) {
                    continue;
                }

                $this->validatePlaceholderCreateStruct(
                    $blockCreateStruct->getPlaceholderStruct($placeholderDefinition->getIdentifier()),
                    $placeholderDefinition
                );
            }
        }
    }

    /**
     * Validates block update struct.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\Page\BlockUpdateStruct $blockUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\ValidationFailedException If the validation failed
     */
    public function validateBlockUpdateStruct(Block $block, BlockUpdateStruct $blockUpdateStruct)
    {
        $this->validate(
            $blockUpdateStruct,
            array(
                new BlockUpdateStructConstraint(
                    array(
                        'payload' => $block,
                    )
                ),
            )
        );
    }

    /**
     * Validates placeholder create struct.
     *
     * @param \Netgen\BlockManager\API\Values\Page\PlaceholderCreateStruct $placeholderCreateStruct
     * @param \Netgen\BlockManager\Block\PlaceholderDefinitionInterface $placeholderDefinition
     *
     * @throws \Netgen\BlockManager\Exception\ValidationFailedException If the validation failed
     */
    public function validatePlaceholderCreateStruct(
        PlaceholderCreateStruct $placeholderCreateStruct,
        PlaceholderDefinitionInterface $placeholderDefinition
    ) {
        $this->validate(
            $placeholderCreateStruct,
            array(
                new ParameterStruct(
                    array(
                        'parameterCollection' => $placeholderDefinition,
                    )
                ),
            ),
            'parameterValues'
        );
    }
}
