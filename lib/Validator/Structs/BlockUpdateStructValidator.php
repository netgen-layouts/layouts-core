<?php

namespace Netgen\BlockManager\Validator\Structs;

use Netgen\BlockManager\API\Values\Page\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Validator\Constraint\BlockItemViewType;
use Netgen\BlockManager\Validator\Constraint\BlockViewType;
use Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct;
use Netgen\BlockManager\Validator\Constraint\Structs\BlockUpdateStruct as BlockUpdateStructConstraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class BlockUpdateStructValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param \Symfony\Component\Validator\Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof BlockUpdateStructConstraint) {
            throw new UnexpectedTypeException($constraint, BlockUpdateStructConstraint::class);
        }

        if (!$constraint->payload instanceof Block) {
            throw new UnexpectedTypeException($constraint->payload, Block::class);
        }

        if (!$value instanceof BlockUpdateStruct) {
            throw new UnexpectedTypeException($value, BlockUpdateStruct::class);
        }

        /** @var \Symfony\Component\Validator\Validator\ContextualValidatorInterface $validator */
        $block = $constraint->payload;
        $blockDefinition = $block->getBlockDefinition();
        $validator = $this->context->getValidator()->inContext($this->context);

        if ($value->viewType !== null) {
            $validator->atPath('viewType')->validate(
                $value->viewType,
                array(
                    new Constraints\Type(array('type' => 'string')),
                    new BlockViewType(array('definition' => $blockDefinition)),
                )
            );
        }

        if ($value->itemViewType !== null) {
            $validator->atPath('itemViewType')->validate(
                $value->itemViewType,
                array(
                    new Constraints\Type(array('type' => 'string')),
                    new BlockItemViewType(
                        array(
                            'viewType' => $value->viewType !== null ?
                                $value->viewType :
                                $block->getViewType(),
                            'definition' => $blockDefinition,
                        )
                    ),
                )
            );
        }

        if ($value->name !== null) {
            $validator->atPath('name')->validate(
                $value->name,
                array(
                    new Constraints\Type(array('type' => 'string')),
                )
            );
        }

        $validator->atPath('parameterValues')->validate(
            $value,
            array(
                new ParameterStruct(
                    array(
                        'parameterCollection' => $blockDefinition,
                        'allowMissingFields' => true,
                    )
                ),
            )
        );
    }
}
