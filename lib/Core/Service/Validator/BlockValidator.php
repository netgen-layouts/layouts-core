<?php

namespace Netgen\BlockManager\Core\Service\Validator;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Block\PlaceholderCreateStruct;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Block\ContainerDefinitionInterface;
use Netgen\BlockManager\Block\PlaceholderDefinitionInterface;
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
    protected $configValidator;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\ConfigValidator $configValidator
     */
    public function __construct(ConfigValidator $configValidator)
    {
        $this->configValidator = $configValidator;
    }

    /**
     * Validates block create struct.
     *
     * @param \Netgen\BlockManager\API\Values\Block\BlockCreateStruct $blockCreateStruct
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

        if ($blockCreateStruct->definition instanceof ContainerDefinitionInterface) {
            foreach ($blockCreateStruct->definition->getPlaceholders() as $placeholderDefinition) {
                if ($blockCreateStruct->hasPlaceholderStruct($placeholderDefinition->getIdentifier())) {
                    $this->validatePlaceholderCreateStruct(
                        $blockCreateStruct->getPlaceholderStruct($placeholderDefinition->getIdentifier()),
                        $placeholderDefinition
                    );
                }
            }
        }

        $this->configValidator->validateConfigStructs(
            'block',
            $blockCreateStruct->getConfigStructs()
        );
    }

    /**
     * Validates block update struct.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Netgen\BlockManager\API\Values\Block\BlockUpdateStruct $blockUpdateStruct
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

        $this->configValidator->validateConfigStructs(
            'block',
            $blockUpdateStruct->getConfigStructs()
        );
    }

    /**
     * Validates placeholder create struct.
     *
     * @param \Netgen\BlockManager\API\Values\Block\PlaceholderCreateStruct $placeholderCreateStruct
     * @param \Netgen\BlockManager\Block\PlaceholderDefinitionInterface $placeholderDefinition
     *
     * @throws \Netgen\BlockManager\Exception\ValidationFailedException If the validation failed
     */
    protected function validatePlaceholderCreateStruct(
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
