<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Constraint\ConditionType;

use Netgen\Layouts\Validator\Constraint\ConditionType\Time;
use PHPUnit\Framework\TestCase;

final class TimeTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Validator\Constraint\ConditionType\Time::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new Time();
        self::assertSame('nglayouts_condition_type_time', $constraint->validatedBy());
    }
}
