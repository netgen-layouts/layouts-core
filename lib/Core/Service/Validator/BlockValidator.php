<?php

namespace Netgen\BlockManager\Core\Service\Validator;

use Netgen\BlockManager\API\Values\BlockCreateStruct as APIBlockCreateStruct;
use Netgen\BlockManager\API\Values\BlockUpdateStruct as APIBlockUpdateStruct;
use Netgen\BlockManager\API\Values\Page\Block as APIBlock;
use Netgen\BlockManager\Validator\Constraint\BlockDefinition;
use Netgen\BlockManager\Validator\Constraint\BlockParameters;
use Netgen\BlockManager\Validator\Constraint\BlockViewType;
use Symfony\Component\Validator\Constraints;

class BlockValidator extends Validator
{
    /**
     * Validates block create struct.
     *
     * @param \Netgen\BlockManager\API\Values\BlockCreateStruct $blockCreateStruct
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If the validation failed
     */
    public function validateBlockCreateStruct(APIBlockCreateStruct $blockCreateStruct)
    {
        $this->validate(
            $blockCreateStruct->definitionIdentifier,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'string')),
                new BlockDefinition(),
            ),
            'definitionIdentifier'
        );

        $this->validate(
            $blockCreateStruct->viewType,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'string')),
                new BlockViewType(array('definitionIdentifier' => $blockCreateStruct->definitionIdentifier)),
            ),
            'viewType'
        );

        $this->validate(
            $blockCreateStruct->name,
            array(
                new Constraints\Type(array('type' => 'string')),
            ),
            'name'
        );

        $this->validate(
            $blockCreateStruct->getParameters(),
            array(
                new BlockParameters(array('definitionIdentifier' => $blockCreateStruct->definitionIdentifier)),
            )
        );
    }

    /**
     * Validates block update struct.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\BlockUpdateStruct $blockUpdateStruct
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If the validation failed
     */
    public function validateBlockUpdateStruct(APIBlock $block, APIBlockUpdateStruct $blockUpdateStruct)
    {
        $this->validate(
            $blockUpdateStruct->viewType,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'string')),
                new BlockViewType(array('definitionIdentifier' => $block->getDefinitionIdentifier())),
            ),
            'viewType'
        );

        $this->validate(
            $blockUpdateStruct->name,
            array(
                new Constraints\Type(array('type' => 'string')),
            ),
            'name'
        );

        $this->validate(
            $blockUpdateStruct->getParameters(),
            array(
                new BlockParameters(array('definitionIdentifier' => $block->getDefinitionIdentifier())),
            )
        );
    }
}
