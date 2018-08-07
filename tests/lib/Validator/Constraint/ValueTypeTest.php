<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Validator\Constraint;

use Netgen\BlockManager\Validator\Constraint\ValueType;
use PHPUnit\Framework\TestCase;

final class ValueTypeTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\ValueType::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new ValueType();
        self::assertSame('ngbm_value_type', $constraint->validatedBy());
    }
}
