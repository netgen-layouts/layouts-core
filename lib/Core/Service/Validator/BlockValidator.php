<?php

namespace Netgen\BlockManager\Core\Service\Validator;

use Netgen\BlockManager\API\Values\BlockCreateStruct;
use Netgen\BlockManager\API\Values\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\Validator\Constraint\BlockItemViewType;
use Netgen\BlockManager\Validator\Constraint\BlockViewType;
use Netgen\BlockManager\Validator\Constraint\Structs\BlockUpdateStruct as BlockUpdateStructConstraint;
use Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct;
use Symfony\Component\Validator\Constraints;

class BlockValidator extends Validator
{
    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface
     */
    protected $blockDefinitionRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface $blockDefinitionRegistry
     */
    public function __construct(BlockDefinitionRegistryInterface $blockDefinitionRegistry)
    {
        $this->blockDefinitionRegistry = $blockDefinitionRegistry;
    }

    /**
     * Validates block create struct.
     *
     * @param \Netgen\BlockManager\API\Values\BlockCreateStruct $blockCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the validation failed
     */
    public function validateBlockCreateStruct(BlockCreateStruct $blockCreateStruct)
    {
        $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition(
            $blockCreateStruct->definitionIdentifier
        );

        $this->validate(
            $blockCreateStruct->definitionIdentifier,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'string')),
            ),
            'definitionIdentifier'
        );

        $this->validate(
            $blockCreateStruct->viewType,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'string')),
                new BlockViewType(array('definition' => $blockDefinition)),
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
                        'definition' => $blockDefinition,
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
                        'parameters' => $blockDefinition->getParameters(),
                        'required' => true,
                    )
                ),
            ),
            'parameters'
        );
    }

    /**
     * Validates block update struct.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\BlockUpdateStruct $blockUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the validation failed
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
}
