<?php

namespace Netgen\BlockManager\Core\Service\Validator;

use Netgen\BlockManager\API\Values\BlockCreateStruct;
use Netgen\BlockManager\API\Values\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\Validator\Constraint\BlockDefinition;
use Netgen\BlockManager\Validator\Constraint\BlockViewType;
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
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If the validation failed
     */
    public function validateBlockCreateStruct(BlockCreateStruct $blockCreateStruct)
    {
        $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition($blockCreateStruct->definitionIdentifier);

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
                new BlockViewType(array('definition' => $blockDefinition)),
            ),
            'viewType'
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

        $fields = $this->buildParameterValidationFields($blockDefinition->getParameters());

        $this->validate(
            $blockCreateStruct->getParameters(),
            array(
                new Constraints\Collection(array('fields' => $fields)),
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
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If the validation failed
     */
    public function validateBlockUpdateStruct(Block $block, BlockUpdateStruct $blockUpdateStruct)
    {
        $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition($block->getDefinitionIdentifier());

        if ($blockUpdateStruct->viewType !== null) {
            $this->validate(
                $blockUpdateStruct->viewType,
                array(
                    new Constraints\NotBlank(),
                    new Constraints\Type(array('type' => 'string')),
                    new BlockViewType(array('definition' => $blockDefinition)),
                ),
                'viewType'
            );
        }

        if ($blockUpdateStruct->name !== null) {
            $this->validate(
                $blockUpdateStruct->name,
                array(
                    new Constraints\Type(array('type' => 'string')),
                ),
                'name'
            );
        }

        $fields = $this->buildParameterValidationFields($blockDefinition->getParameters(), false);

        $this->validate(
            $blockUpdateStruct->getParameters(),
            array(
                new Constraints\Collection(array('fields' => $fields)),
            ),
            'parameters'
        );
    }
}
