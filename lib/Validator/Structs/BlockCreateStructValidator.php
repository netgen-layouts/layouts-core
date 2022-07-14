<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Structs;

use Netgen\Layouts\API\Values\Block\BlockCreateStruct;
use Netgen\Layouts\Validator\Constraint\BlockItemViewType;
use Netgen\Layouts\Validator\Constraint\BlockViewType;
use Netgen\Layouts\Validator\Constraint\Structs\BlockCreateStruct as BlockCreateStructConstraint;
use Netgen\Layouts\Validator\Constraint\Structs\ParameterStruct;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use function sprintf;

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

        if (!isset($value->viewType)) {
            $this->context->buildViolation(sprintf('"viewType" is required in %s', BlockCreateStruct::class))
                ->addViolation();

            return;
        }

        if (!isset($value->itemViewType)) {
            $this->context->buildViolation(sprintf('"itemViewType" is required in %s', BlockCreateStruct::class))
                ->addViolation();

            return;
        }

        if (!isset($value->isTranslatable)) {
            $this->context->buildViolation(sprintf('"isTranslatable" is required in %s', BlockCreateStruct::class))
                ->addViolation();

            return;
        }

        if (!isset($value->alwaysAvailable)) {
            $this->context->buildViolation(sprintf('"alwaysAvailable" is required in %s', BlockCreateStruct::class))
                ->addViolation();

            return;
        }

        /** @var \Symfony\Component\Validator\Validator\ContextualValidatorInterface $validator */
        $validator = $this->context->getValidator()->inContext($this->context);

        $blockDefinition = $value->getDefinition();

        $validator->atPath('viewType')->validate(
            $value->viewType,
            [
                new BlockViewType(['definition' => $blockDefinition]),
            ],
        );

        $validator->atPath('itemViewType')->validate(
            $value->itemViewType,
            [
                new BlockItemViewType(
                    [
                        'viewType' => $value->viewType,
                        'definition' => $blockDefinition,
                    ],
                ),
            ],
        );

        $validator->atPath('parameterValues')->validate(
            $value,
            [
                new ParameterStruct(
                    [
                        'parameterDefinitions' => $blockDefinition,
                    ],
                ),
            ],
        );
    }
}
