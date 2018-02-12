<?php

namespace Netgen\BlockManager\Tests\Validator\Constraint\Parameters;

use Netgen\BlockManager\Validator\Constraint\Parameters\DateTime;
use PHPUnit\Framework\TestCase;

final class DateTimeTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\Parameters\DateTime::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new DateTime();
        $this->assertEquals('ngbm_datetime', $constraint->validatedBy());
    }
}
