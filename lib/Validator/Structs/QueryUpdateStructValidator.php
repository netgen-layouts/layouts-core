<?php

namespace Netgen\BlockManager\Validator\Structs;

use Netgen\BlockManager\Validator\Constraint\Parameters;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraint;

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
        /** @var \Netgen\BlockManager\API\Values\Collection\Query $query */
        /** @var \Netgen\BlockManager\API\Values\QueryUpdateStruct $value */
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
                new Parameters(
                    array(
                        'parameters' => $queryType->getParameters(),
                        'required' => false,
                    )
                ),
            )
        );
    }
}
