<?php

namespace Netgen\BlockManager\Core\Service\Validator;

use Netgen\BlockManager\API\Values\BlockCreateStruct;
use Netgen\BlockManager\API\Values\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\Validator\Constraint\BlockDefinition;
use Netgen\BlockManager\Validator\Constraint\BlockViewType;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints;

class BlockValidator extends Validator
{
    /**
     * @var \Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistryInterface
     */
    protected $blockDefinitionRegistry;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\Validator\Validator\ValidatorInterface $validator
     * @param \Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistryInterface $blockDefinitionRegistry
     */
    public function __construct(ValidatorInterface $validator, BlockDefinitionRegistryInterface $blockDefinitionRegistry)
    {
        parent::__construct($validator);

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

        if ($blockCreateStruct->name !== null) {
            $this->validate(
                $blockCreateStruct->name,
                array(
                    new Constraints\Type(array('type' => 'string')),
                ),
                'name'
            );
        }

        $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition($blockCreateStruct->definitionIdentifier);
        $fields = $this->buildParameterValidationFields(
            $blockDefinition->getParameters(),
            $blockDefinition->getParameterConstraints()
        );

        $this->validate(
            $blockCreateStruct->getParameters(),
            array(
                new Constraints\Collection(
                    array(
                        'fields' => $fields,
                        'allowExtraFields' => false,
                        'allowMissingFields' => true,
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
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If the validation failed
     */
    public function validateBlockUpdateStruct(Block $block, BlockUpdateStruct $blockUpdateStruct)
    {
        if ($blockUpdateStruct->viewType !== null) {
            $this->validate(
                $blockUpdateStruct->viewType,
                array(
                    new Constraints\NotBlank(),
                    new Constraints\Type(array('type' => 'string')),
                    new BlockViewType(array('definitionIdentifier' => $block->getDefinitionIdentifier())),
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

        $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition($block->getDefinitionIdentifier());
        $fields = $this->buildParameterValidationFields(
            $blockDefinition->getParameters(),
            $blockDefinition->getParameterConstraints()
        );

        $this->validate(
            $blockUpdateStruct->getParameters(),
            array(
                new Constraints\Collection(
                    array(
                        'fields' => $fields,
                        'allowExtraFields' => false,
                        'allowMissingFields' => true,
                    )
                ),
            ),
            'parameters'
        );
    }
}
