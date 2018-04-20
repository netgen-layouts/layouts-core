<?php

namespace Netgen\BlockManager\Validator\Structs;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct;
use Netgen\BlockManager\Validator\Constraint\Structs\QueryUpdateStruct as QueryUpdateStructConstraint;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates the complete QueryUpdateStruct value.
 */
final class QueryUpdateStructValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
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

        /** @var \Symfony\Component\Validator\Validator\ContextualValidatorInterface $validator */
        $query = $constraint->payload;
        $validator = $this->context->getValidator()->inContext($this->context);

        $validator->atPath('locale')->validate(
            $value->locale,
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'string']),
                new Constraints\Locale(),
            ]
        );

        $validator->atPath('parameterValues')->validate(
            $value,
            [
                new ParameterStruct(
                    [
                        'parameterDefinitions' => $query->getQueryType(),
                        'allowMissingFields' => true,
                    ]
                ),
            ]
        );
    }
}
