<?php

namespace Netgen\BlockManager\Tests\Validator\Constraint\Parameters;

use Netgen\BlockManager\Validator\Constraint\DateTime;
use PHPUnit\Framework\TestCase;

final class DateTimeTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\DateTime::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new DateTime();
        $this->assertEquals('ngbm_datetime', $constraint->validatedBy());
    }
}
