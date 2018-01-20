<?php

namespace Netgen\BlockManager\Tests\Validator\Constraint;

use Netgen\BlockManager\Validator\Constraint\ValueType;
use PHPUnit\Framework\TestCase;

final class ValueTypeTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\ValueType::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new ValueType();
        $this->assertEquals('ngbm_value_type', $constraint->validatedBy());
    }
}
