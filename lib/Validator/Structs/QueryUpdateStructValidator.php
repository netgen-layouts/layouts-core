<?php

namespace Netgen\BlockManager\Validator\Structs;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\Parameters\CompoundParameterInterface;
use Netgen\BlockManager\Parameters\ParameterCollectionInterface;
use Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct;
use Netgen\BlockManager\Validator\Constraint\Structs\QueryUpdateStruct as QueryUpdateStructConstraint;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ConstraintValidator;
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
        $validator = $this->context->getValidator()->inContext($this->context);

        $validator->atPath('locale')->validate(
            $value->locale,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'string')),
                new Constraints\Locale(),
            )
        );

        if ($value->locale !== $query->getMainLocale()) {
            if (!$this->validateUntranslatableParameters($query->getQueryType(), $value, $constraint)) {
                return;
            }
        }

        $validator->atPath('parameterValues')->validate(
            $value,
            array(
                new ParameterStruct(
                    array(
                        'parameterCollection' => $query->getQueryType(),
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
     * @param \Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct $queryUpdateStruct
     * @param \Netgen\BlockManager\Validator\Constraint\Structs\QueryUpdateStruct $constraint
     *
     * @return bool
     */
    protected function validateUntranslatableParameters(
        ParameterCollectionInterface $parameterCollection,
        QueryUpdateStruct $queryUpdateStruct,
        QueryUpdateStructConstraint $constraint
    ) {
        foreach ($parameterCollection->getParameters() as $parameterName => $parameter) {
            if (!$parameter->getOption('translatable') && $queryUpdateStruct->hasParameterValue($parameterName)) {
                $this->context->buildViolation($constraint->untranslatableMessage)
                    ->setParameter('%parameterName%', $parameterName)
                    ->setParameter('%mainLocale%', $constraint->payload->getMainLocale())
                    ->addViolation();

                return false;
            }

            if ($parameter instanceof CompoundParameterInterface) {
                if (!$this->validateUntranslatableParameters($parameter, $queryUpdateStruct, $constraint)) {
                    return false;
                }
            }
        }

        return true;
    }
}
