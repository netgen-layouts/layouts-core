<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Locale;

use Netgen\Layouts\Exception\Locale\LocaleException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LocaleException::class)]
final class LocaleExceptionTest extends TestCase
{
    public function testNoLocale(): void
    {
        $exception = LocaleException::noLocale();

        self::assertSame(
            'No locales available in the current context.',
            $exception->getMessage(),
        );
    }
}
