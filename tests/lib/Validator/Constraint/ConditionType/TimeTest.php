<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Validator\Constraint\ConditionType;

use Netgen\BlockManager\Validator\Constraint\ConditionType\Time;
use PHPUnit\Framework\TestCase;

final class TimeTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\ConditionType\Time::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new Time();
        $this->assertSame('ngbm_condition_type_time', $constraint->validatedBy());
    }
}
