<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Structs;

use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\API\Values\Collection\QueryUpdateStruct;
use Netgen\Layouts\Validator\Constraint\Locale as LocaleConstraint;
use Netgen\Layouts\Validator\Constraint\Structs\ParameterStruct;
use Netgen\Layouts\Validator\Constraint\Structs\QueryUpdateStruct as QueryUpdateStructConstraint;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use function sprintf;

/**
 * Validates the complete QueryUpdateStruct value.
 */
final class QueryUpdateStructValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof QueryUpdateStructConstraint) {
            throw new UnexpectedTypeException($constraint, QueryUpdateStructConstraint::class);
        }

        if (!$constraint->payload instanceof Query) {
            throw new UnexpectedTypeException($constraint->payload, Query::class);
        }

        if (!$value instanceof QueryUpdateStruct) {
            throw new UnexpectedTypeException($value, QueryUpdateStruct::class);
        }

        if (!isset($value->locale)) {
            $this->context->buildViolation(sprintf('"locale" is required in %s', QueryUpdateStruct::class))
                ->addViolation();

            return;
        }

        /** @var \Symfony\Component\Validator\Validator\ContextualValidatorInterface $validator */
        $validator = $this->context->getValidator()->inContext($this->context);

        $validator->atPath('locale')->validate(
            $value->locale,
            [
                new Constraints\NotBlank(),
                new LocaleConstraint(),
            ],
        );

        $validator->atPath('parameterValues')->validate(
            $value,
            [
                new ParameterStruct(
                    [
                        'parameterDefinitions' => $constraint->payload->getQueryType(),
                        'allowMissingFields' => true,
                    ],
                ),
            ],
        );
    }
}
