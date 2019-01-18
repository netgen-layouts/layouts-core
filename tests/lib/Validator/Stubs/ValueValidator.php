<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Validator\Stubs;

use Netgen\BlockManager\Validator\ValidatorTrait;

final class ValueValidator
{
    use ValidatorTrait;

    /**
     * @param mixed $value
     * @param \Symfony\Component\Validator\Constraint[] $constraints
     * @param string|null $propertyPath
     */
    public function validateValue($value, $constraints, ?string $propertyPath = null): void
    {
        $this->validate($value, $constraints, $propertyPath);
    }
}
