<?php

namespace Netgen\BlockManager\Core\Service\Validator;

use Netgen\BlockManager\API\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
                $violation->getInvalidValue(),
                $violation->getMessage()
            );
        }
    }
}
