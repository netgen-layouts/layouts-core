<?php

namespace Netgen\BlockManager\Validator\Structs;

use Netgen\BlockManager\Validator\Constraint\BlockItemViewType;
use Netgen\BlockManager\Validator\Constraint\BlockViewType;
use Netgen\BlockManager\Validator\Constraint\Parameters;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraint;

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
        /** @var \Netgen\BlockManager\API\Values\Page\Block $block */
        /** @var \Netgen\BlockManager\API\Values\BlockUpdateStruct $value */
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

        $validator->atPath('parameters')->validate(
            $value,
            array(
                new Parameters(
                    array(
                        'parameters' => $blockDefinition->getParameters(),
                        'required' => false,
                    )
                ),
            )
        );
    }
}
