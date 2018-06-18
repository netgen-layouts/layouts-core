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
    public function testValidatedBy(): void
    {
        $constraint = new DateTime();
        $this->assertSame('ngbm_datetime', $constraint->validatedBy());
    }
}
