<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Locale;

use Netgen\Layouts\Locale\LocaleProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

#[CoversClass(LocaleProvider::class)]
final class LocaleProviderTest extends TestCase
{
    public function testGetAvailableLocales(): void
    {
        $localeProvider = new LocaleProvider(['en', 'hr']);

        self::assertSame(
            [
                'en' => 'English',
                'hr' => 'Croatian',
            ],
            $localeProvider->getAvailableLocales(),
        );
    }

    public function testGetAvailableLocalesWithNonExistingLocales(): void
    {
        $localeProvider = new LocaleProvider(['en', 'hr_NON_EXISTING']);

        self::assertSame(
            [
                'en' => 'English',
            ],
            $localeProvider->getAvailableLocales(),
        );
    }

    public function testGetAvailableLocalesWithDefaultLocales(): void
    {
        $localeProvider = new LocaleProvider();

        self::assertNotEmpty($localeProvider->getAvailableLocales());
    }

    public function testGetRequestLocales(): void
    {
        $localeProvider = new LocaleProvider();

        $requestStub = self::createStub(Request::class);
        $requestStub
            ->method('getLocale')
            ->willReturn('en');

        self::assertSame(['en'], $localeProvider->getRequestLocales($requestStub));
    }

    public function testGetRequestLocalesWithEnabledLocales(): void
    {
        $localeProvider = new LocaleProvider(['en', 'hr']);

        $requestStub = self::createStub(Request::class);
        $requestStub
            ->method('getLocale')
            ->willReturn('en');

        self::assertSame(['en'], $localeProvider->getRequestLocales($requestStub));
    }

    public function testGetRequestLocalesWithNonEnabledLocale(): void
    {
        $localeProvider = new LocaleProvider(['en', 'hr']);

        $requestStub = self::createStub(Request::class);
        $requestStub
            ->method('getLocale')
            ->willReturn('de');

        self::assertSame([], $localeProvider->getRequestLocales($requestStub));
    }
}
