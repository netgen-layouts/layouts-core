<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Constraint\ConditionType;

use Netgen\Layouts\Validator\Constraint\ConditionType\Time;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Time::class)]
final class TimeTest extends TestCase
{
    public function testValidatedBy(): void
    {
        $constraint = new Time();
        self::assertSame('nglayouts_condition_type_time', $constraint->validatedBy());
    }
}
