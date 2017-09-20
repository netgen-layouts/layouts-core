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

class BlockValidator extends Validator
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\ConfigValidator
     */
    private $configValidator;

    public function __construct(ConfigValidator $configValidator)
    {
        $this->configValidator = $configValidator;
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
            $blockCreateStruct->isTranslatable,
            array(
                new Constraints\NotNull(),
                new Constraints\Type(array('type' => 'bool')),
            ),
            'isTranslatable'
        );

        $this->validate(
            $blockCreateStruct->alwaysAvailable,
            array(
                new Constraints\NotNull(),
                new Constraints\Type(array('type' => 'bool')),
            ),
            'alwaysAvailable'
        );

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

        $this->configValidator->validateConfigStructs(
            $blockCreateStruct->getConfigStructs(),
            $blockCreateStruct->definition->getConfigDefinitions()
        );
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
            array(
                new BlockUpdateStructConstraint(
                    array(
                        'payload' => $block,
                    )
                ),
            )
        );

        $this->configValidator->validateConfigStructs(
            $blockUpdateStruct->getConfigStructs(),
            $block->getDefinition()->getConfigDefinitions()
        );
    }
}
