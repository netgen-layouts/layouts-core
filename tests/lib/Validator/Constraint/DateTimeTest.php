<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Validator\Constraint;

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
