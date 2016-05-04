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
     * Validates the provided ID.
     *
     * @param int|string $id
     * @param string $propertyPath
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If the validation failed
     */
    public function validateId($id, $propertyPath = null)
    {
        $this->validate(
            $id,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'scalar')),
            ),
            $propertyPath
        );
    }

    /**
     * Validates the provided identifier.
     *
     * @param string $identifier
     * @param string $propertyPath
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If the validation failed
     */
    public function validateIdentifier($identifier, $propertyPath = null)
    {
        $this->validate(
            $identifier,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'string')),
            ),
            $propertyPath
        );
    }

    /**
     * Validates the provided position.
     *
     * @param int $position
     * @param string $propertyPath
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If the validation failed
     */
    public function validatePosition($position, $propertyPath = null)
    {
        $this->validate(
            $position,
            array(
                new Constraints\GreaterThanOrEqual(0),
                new Constraints\Type(array('type' => 'int')),
            ),
            $propertyPath
        );
    }

    /**
     * Builds the "fields" array from provided parameters and constraints, used for validating set of parameters.
     *
     * @param \Netgen\BlockManager\Parameters\Parameter[] $parameters
     * @param array $constraints
     * @param bool $useRequired
     *
     * @return array
     */
    protected function buildParameterValidationFields(array $parameters, array $constraints, $useRequired = true)
    {
        $fields = array();
        foreach ($parameters as $parameterName => $parameter) {
            $paramConstraints = array();
            if (isset($constraints[$parameterName]) && is_array($constraints[$parameterName])) {
                $paramConstraints = $constraints[$parameterName];
            }

            if ($useRequired && $parameter->isRequired()) {
                $fields[$parameterName] = new Constraints\Required($paramConstraints);
            } else {
                $fields[$parameterName] = new Constraints\Optional($paramConstraints);
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
