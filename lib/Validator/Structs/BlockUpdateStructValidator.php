<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Structs;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\BlockUpdateStruct;
use Netgen\Layouts\Validator\Constraint\BlockItemViewType;
use Netgen\Layouts\Validator\Constraint\BlockViewType;
use Netgen\Layouts\Validator\Constraint\Locale as LocaleConstraint;
use Netgen\Layouts\Validator\Constraint\Structs\BlockUpdateStruct as BlockUpdateStructConstraint;
use Netgen\Layouts\Validator\Constraint\Structs\ParameterStruct;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates the complete BlockUpdateStruct value.
 */
final class BlockUpdateStructValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
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

        $block = $constraint->payload;
        $blockDefinition = $block->getDefinition();

        /** @var \Symfony\Component\Validator\Validator\ContextualValidatorInterface $validator */
        $validator = $this->context->getValidator()->inContext($this->context);

        $validator->atPath('locale')->validate(
            $value->locale,
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'string']),
                new LocaleConstraint(),
            ]
        );

        if ($value->viewType !== null) {
            $validator->atPath('viewType')->validate(
                $value->viewType,
                [
                    new Constraints\Type(['type' => 'string']),
                    new BlockViewType(['definition' => $blockDefinition]),
                ]
            );
        }

        if ($value->itemViewType !== null) {
            $validator->atPath('itemViewType')->validate(
                $value->itemViewType,
                [
                    new Constraints\Type(['type' => 'string']),
                    new BlockItemViewType(
                        [
                            'viewType' => $value->viewType ?? $block->getViewType(),
                            'definition' => $blockDefinition,
                        ]
                    ),
                ]
            );
        }

        if ($value->name !== null) {
            $validator->atPath('name')->validate(
                $value->name,
                [
                    new Constraints\Type(['type' => 'string']),
                ]
            );
        }

        if ($value->alwaysAvailable !== null) {
            $validator->atPath('alwaysAvailable')->validate(
                $value->alwaysAvailable,
                [
                    new Constraints\Type(['type' => 'bool']),
                ]
            );
        }

        $validator->atPath('parameterValues')->validate(
            $value,
            [
                new ParameterStruct(
                    [
                        'parameterDefinitions' => $blockDefinition,
                        'allowMissingFields' => true,
                    ]
                ),
            ]
        );
    }
}
