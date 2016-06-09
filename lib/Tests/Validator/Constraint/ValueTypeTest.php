<?php

namespace Netgen\BlockManager\Tests\Validator\Constraint;

use Netgen\BlockManager\Validator\Constraint\ValueType;

class ValueTypeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\ValueType::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new ValueType();
        self::assertEquals('ngbm_value_type', $constraint->validatedBy());
    }
}
