<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Locale;

use Netgen\Layouts\Exception\Locale\LocaleException;
use PHPUnit\Framework\TestCase;

final class LocaleExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Exception\Locale\LocaleException::noLocale
     */
    public function testNoLocale(): void
    {
        $exception = LocaleException::noLocale();

        self::assertSame(
            'No locales available in the current context.',
            $exception->getMessage(),
        );
    }
}
