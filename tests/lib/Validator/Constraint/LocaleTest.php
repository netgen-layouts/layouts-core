<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Validator\Constraint;

use Netgen\BlockManager\Validator\Constraint\Locale;
use PHPUnit\Framework\TestCase;

final class LocaleTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\Locale::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new Locale();
        self::assertSame('ngbm_locale', $constraint->validatedBy());
    }
}
