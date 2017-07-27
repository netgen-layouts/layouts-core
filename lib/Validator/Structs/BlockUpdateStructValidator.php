<?php

namespace Netgen\BlockManager\Validator\Structs;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\Parameters\CompoundParameterInterface;
use Netgen\BlockManager\Parameters\ParameterCollectionInterface;
use Netgen\BlockManager\Validator\Constraint\BlockItemViewType;
use Netgen\BlockManager\Validator\Constraint\BlockViewType;
use Netgen\BlockManager\Validator\Constraint\Structs\BlockUpdateStruct as BlockUpdateStructConstraint;
use Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ConstraintValidator;
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
        $blockDefinition = $block->getDefinition();
        $validator = $this->context->getValidator()->inContext($this->context);

        $validator->atPath('locale')->validate(
            $value->locale,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'string')),
                new Constraints\Locale(),
            )
        );

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

        if ($value->alwaysAvailable !== null) {
            $validator->atPath('alwaysAvailable')->validate(
                $value->alwaysAvailable,
                array(
                    new Constraints\Type(array('type' => 'bool')),
                )
            );
        }

        if ($value->locale !== $block->getMainLocale()) {
            if (!$this->validateUntranslatableParameters($block->getDefinition(), $value, $constraint)) {
                return;
            }
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

    /**
     * Validates that only translatable parameters are provided in the update struct.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterCollectionInterface $parameterCollection
     * @param \Netgen\BlockManager\API\Values\Block\BlockUpdateStruct $blockUpdateStruct
     * @param \Netgen\BlockManager\Validator\Constraint\Structs\BlockUpdateStruct $constraint
     *
     * @return bool
     */
    protected function validateUntranslatableParameters(
        ParameterCollectionInterface $parameterCollection,
        BlockUpdateStruct $blockUpdateStruct,
        BlockUpdateStructConstraint $constraint
    ) {
        foreach ($parameterCollection->getParameters() as $parameterName => $parameter) {
            if (!$parameter->getOption('translatable') && $blockUpdateStruct->hasParameterValue($parameterName)) {
                $this->context->buildViolation($constraint->untranslatableMessage)
                    ->setParameter('%parameterName%', $parameterName)
                    ->setParameter('%mainLocale%', $constraint->payload->getMainLocale())
                    ->addViolation();

                return false;
            }

            if ($parameter instanceof CompoundParameterInterface) {
                if (!$this->validateUntranslatableParameters($parameter, $blockUpdateStruct, $constraint)) {
                    return false;
                }
            }
        }

        return true;
    }
}
