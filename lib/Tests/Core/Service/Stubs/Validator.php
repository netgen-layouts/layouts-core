<?php

namespace Netgen\BlockManager\Tests\Core\Service\Stubs;

use Netgen\BlockManager\Core\Service\Validator\Validator as BaseValidator;

class Validator extends BaseValidator
{
    /**
     * Validates the value against a set of provided constraints.
     *
     * @param mixed $value
     * @param \Symfony\Component\Validator\Constraint[] $constraints
     * @param string $propertyPath
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If the validation failed
     */
    public function validate($value, array $constraints, $propertyPath = null)
    {
        parent::validate($value, $constraints, $propertyPath);
    }
}
