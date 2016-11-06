<?php

namespace Netgen\BlockManager\Validator\Structs;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\QueryUpdateStruct;
use Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct;
use Netgen\BlockManager\Validator\Constraint\Structs\QueryUpdateStruct as QueryUpdateStructConstraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class QueryUpdateStructValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param \Symfony\Component\Validator\Constraint $constraint The constraint for the validation
     */
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
        $queryType = $query->getQueryType();
        $validator = $this->context->getValidator()->inContext($this->context);

        if ($value->identifier !== null) {
            $validator->atPath('identifier')->validate(
                $value->identifier,
                array(
                    new Constraints\NotBlank(),
                    new Constraints\Type(array('type' => 'string')),
                )
            );
        }

        $validator->atPath('parameters')->validate(
            $value,
            array(
                new ParameterStruct(
                    array(
                        'parameterCollection' => $queryType,
                        'allowMissingFields' => true,
                    )
                ),
            )
        );
    }
}
