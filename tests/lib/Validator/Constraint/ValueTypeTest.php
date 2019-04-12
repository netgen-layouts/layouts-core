<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Constraint;

use Netgen\Layouts\Validator\Constraint\ValueType;
use PHPUnit\Framework\TestCase;

final class ValueTypeTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Validator\Constraint\ValueType::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new ValueType();
        self::assertSame('nglayouts_value_type', $constraint->validatedBy());
    }
}
