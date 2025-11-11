<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Constraint;

use Netgen\Layouts\Validator\Constraint\ValueType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ValueType::class)]
final class ValueTypeTest extends TestCase
{
    public function testValidatedBy(): void
    {
        $constraint = new ValueType();
        self::assertSame('nglayouts_value_type', $constraint->validatedBy());
    }
}
