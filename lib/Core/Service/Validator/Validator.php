<?php

namespace Netgen\BlockManager\Core\Service\Validator;

use Netgen\BlockManager\API\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints;

abstract class Validator
{
    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    protected $validator;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\Validator\Validator\ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Builds the "fields" array from provided parameters and constraints, used for validating set of parameters.
     *
     * @param array $parameters
     * @param array $parameterConstraints
     *
     * @return array
     */
    protected function buildParameterValidationFields(array $parameters, array $parameterConstraints)
    {
        $fields = array();
        foreach ($parameters as $parameterName => $parameter) {
            if (isset($parameterConstraints[$parameterName]) && is_array($parameterConstraints[$parameterName])) {
                $fields[$parameterName] = $parameterConstraints[$parameterName];
            } else {
                $fields[$parameterName] = array();
            }
        }

        return $fields;
    }

    /**
     * Validates the value against a set of provided constraints.
     *
     * @param mixed $value
     * @param \Symfony\Component\Validator\Constraint[] $constraints
     * @param string $propertyPath
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If the validation failed
     */
    protected function validate($value, array $constraints, $propertyPath = null)
    {
        $violations = $this->validator->validate($value, $constraints);

        if ($violations->count() > 0) {
            $violation = $violations->offsetGet(0);

            throw new InvalidArgumentException(
                $propertyPath !== null ? $propertyPath : $violation->getPropertyPath(),
                $violation->getMessage()
            );
        }
    }
}
