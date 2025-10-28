<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Stubs;

use Netgen\Layouts\Validator\ValidatorTrait;

final class ValueValidator
{
    use ValidatorTrait;

    /**
     * @param \Symfony\Component\Validator\Constraint[] $constraints
     */
    public function validateValue(mixed $value, array $constraints, ?string $propertyPath = null): void
    {
        $this->validate($value, $constraints, $propertyPath);
    }
}
