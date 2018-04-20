<?php

namespace Netgen\BlockManager\Validator\Structs;

use Netgen\BlockManager\API\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Validator\Constraint\BlockItemViewType;
use Netgen\BlockManager\Validator\Constraint\BlockViewType;
use Netgen\BlockManager\Validator\Constraint\Structs\BlockCreateStruct as BlockCreateStructConstraint;
use Netgen\BlockManager\Validator\Constraint\Structs\ConfigAwareStruct;
use Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates the complete BlockCreateStruct value.
 */
final class BlockCreateStructValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof BlockCreateStructConstraint) {
            throw new UnexpectedTypeException($constraint, BlockCreateStructConstraint::class);
        }

        if (!$value instanceof BlockCreateStruct) {
            throw new UnexpectedTypeException($value, BlockCreateStruct::class);
        }

        if (!$value->definition instanceof BlockDefinitionInterface) {
            throw new UnexpectedTypeException($value->definition, BlockDefinitionInterface::class);
        }

        /** @var \Symfony\Component\Validator\Validator\ContextualValidatorInterface $validator */
        $validator = $this->context->getValidator()->inContext($this->context);

        $validator->atPath('viewType')->validate(
            $value->viewType,
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'string']),
                new BlockViewType(['definition' => $value->definition]),
            ]
        );

        $validator->atPath('itemViewType')->validate(
            $value->itemViewType,
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'string']),
                new BlockItemViewType(
                    [
                        'viewType' => $value->viewType,
                        'definition' => $value->definition,
                    ]
                ),
            ]
        );

        if ($value->name !== null) {
            $validator->atPath('name')->validate(
                $value->name,
                [
                    new Constraints\Type(['type' => 'string']),
                ]
            );
        }

        $validator->atPath('isTranslatable')->validate(
            $value->isTranslatable,
            [
                new Constraints\NotNull(),
                new Constraints\Type(['type' => 'bool']),
            ]
        );

        $validator->atPath('alwaysAvailable')->validate(
            $value->alwaysAvailable,
            [
                new Constraints\NotNull(),
                new Constraints\Type(['type' => 'bool']),
            ]
        );

        $validator->atPath('parameterValues')->validate(
            $value,
            [
                new ParameterStruct(
                    [
                        'parameterCollection' => $value->definition,
                    ]
                ),
            ]
        );

        $validator->validate(
            $value,
            new ConfigAwareStruct(
                [
                    'payload' => $value->definition,
                ]
            )
        );
    }
}
