<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Constraint;

use Netgen\Layouts\Validator\Constraint\DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DateTime::class)]
final class DateTimeTest extends TestCase
{
    public function testValidatedBy(): void
    {
        $constraint = new DateTime();
        self::assertSame('nglayouts_datetime', $constraint->validatedBy());
    }
}
