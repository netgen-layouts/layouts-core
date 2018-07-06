<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Validator\Stubs;

use Netgen\BlockManager\Validator\ValidatorTrait;

final class ValidatorValue
{
    use ValidatorTrait;

    public function validateValue($value, $constraints, ?string $propertyPath = null): void
    {
        $this->validate($value, $constraints, $propertyPath);
    }
}
