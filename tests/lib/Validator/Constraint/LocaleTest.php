<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Constraint;

use Netgen\Layouts\Validator\Constraint\Locale;
use PHPUnit\Framework\TestCase;

final class LocaleTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Validator\Constraint\Locale::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new Locale();
        self::assertSame('nglayouts_locale', $constraint->validatedBy());
    }
}
