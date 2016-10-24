<?php

namespace Netgen\BlockManager\Core\Service\Validator;

use Netgen\BlockManager\API\Values\BlockCreateStruct;
use Netgen\BlockManager\API\Values\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\Validator\Constraint\Structs\BlockUpdateStruct as BlockUpdateStructConstraint;
use Netgen\BlockManager\Validator\Constraint\Structs\BlockCreateStruct as BlockCreateStructConstraint;

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
            $blockCreateStruct,
            array(
                new BlockCreateStructConstraint(
                    array(
                        'payload' => $blockDefinition,
                    )
                ),
            )
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
