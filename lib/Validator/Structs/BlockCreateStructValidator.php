<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Structs;

use Netgen\Layouts\API\Values\Block\BlockCreateStruct;
use Netgen\Layouts\Validator\Constraint\BlockItemViewType;
use Netgen\Layouts\Validator\Constraint\BlockViewType;
use Netgen\Layouts\Validator\Constraint\Structs\BlockCreateStruct as BlockCreateStructConstraint;
use Netgen\Layouts\Validator\Constraint\Structs\ParameterStruct;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates the complete BlockCreateStruct value.
 */
final class BlockCreateStructValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof BlockCreateStructConstraint) {
            throw new UnexpectedTypeException($constraint, BlockCreateStructConstraint::class);
        }

        if (!$value instanceof BlockCreateStruct) {
            throw new UnexpectedTypeException($value, BlockCreateStruct::class);
        }

        /** @var \Symfony\Component\Validator\Validator\ContextualValidatorInterface $validator */
        $validator = $this->context->getValidator()->inContext($this->context);

        $blockDefinition = $value->getDefinition();

        $validator->atPath('viewType')->validate(
            $value->viewType,
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'string']),
                new BlockViewType(['definition' => $blockDefinition]),
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
                        'definition' => $blockDefinition,
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
                        'parameterDefinitions' => $blockDefinition,
                    ]
                ),
            ]
        );
    }
}
