<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Constraint;

use Netgen\Layouts\Validator\Constraint\DateTime;
use PHPUnit\Framework\TestCase;

final class DateTimeTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Validator\Constraint\DateTime::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new DateTime();
        self::assertSame('nglayouts_datetime', $constraint->validatedBy());
    }
}
