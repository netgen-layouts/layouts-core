<?php

namespace Netgen\BlockManager\Validator;

use Netgen\BlockManager\Parameters\CompoundParameterInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraint;

class ParametersValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param \Symfony\Component\Validator\Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        /** @var \Netgen\BlockManager\Validator\Constraint\Parameters $constraint */

        /** @var \Symfony\Component\Validator\Validator\ValidatorInterface $validator */
        $validator = $this->context->getValidator();

        $violations = $validator->validate(
            $value,
            new Constraints\Collection(
                array(
                    'fields' => $this->buildFields(
                        $constraint->parameters,
                        $value,
                        $constraint->required
                    ),
                )
            )
        );

        if ($violations->count() > 0) {
            $violation = $violations->offsetGet(0);

            $this->context->buildViolation($constraint->message)
                ->setParameter('%parameterName%', $violation->getPropertyPath())
                ->setParameter('%message%', $violation->getMessage())
                ->addViolation();
        }
    }

    /**
     * Builds the "fields" array from provided parameters and parameter values.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface[] $parameters
     * @param array $parameterValues
     * @param bool $isRequired
     *
     * @return array
     */
    protected function buildFields(array $parameters, array $parameterValues, $isRequired = true)
    {
        $fields = array();
        foreach ($parameters as $parameterName => $parameter) {
            $fields[$parameterName] = $isRequired ?
                new Constraints\Required($parameter->getConstraints()) :
                new Constraints\Optional($parameter->getConstraints());

            if ($parameter instanceof CompoundParameterInterface) {
                foreach ($parameter->getParameters() as $subParameterName => $subParameter) {
                    $parameterConstraints = $subParameter->getParameterConstraints();
                    if ($subParameter->isRequired() && isset($parameterValues[$parameterName]) && $parameterValues[$parameterName]) {
                        $parameterConstraints = array_merge($parameterConstraints, $subParameter->getBaseConstraints());
                    }

                    $fields[$subParameterName] = $isRequired ?
                        new Constraints\Required($parameterConstraints) :
                        new Constraints\Optional($parameterConstraints);
                }
            }
        }

        return $fields;
    }
}
