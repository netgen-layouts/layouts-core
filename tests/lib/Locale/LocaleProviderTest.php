<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Locale;

use Netgen\BlockManager\Locale\LocaleProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class LocaleProviderTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Locale\LocaleProvider::__construct
     * @covers \Netgen\BlockManager\Locale\LocaleProvider::getAvailableLocales
     */
    public function testGetAvailableLocales(): void
    {
        $localeProvider = new LocaleProvider(['en', 'hr']);

        $this->assertSame(
            [
                'en' => 'English',
                'hr' => 'Croatian',
            ],
            $localeProvider->getAvailableLocales()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Locale\LocaleProvider::__construct
     * @covers \Netgen\BlockManager\Locale\LocaleProvider::getAvailableLocales
     */
    public function testGetAvailableLocalesWithNonExistingLocales(): void
    {
        $localeProvider = new LocaleProvider(['en', 'hr_NON_EXISTING']);

        $this->assertSame(
            [
                'en' => 'English',
            ],
            $localeProvider->getAvailableLocales()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Locale\LocaleProvider::__construct
     * @covers \Netgen\BlockManager\Locale\LocaleProvider::getAvailableLocales
     */
    public function testGetAvailableLocalesWithDefaultLocales(): void
    {
        $localeProvider = new LocaleProvider();

        $this->assertNotEmpty($localeProvider->getAvailableLocales());
    }

    /**
     * @covers \Netgen\BlockManager\Locale\LocaleProvider::getRequestLocales
     */
    public function testGetRequestLocales(): void
    {
        $localeProvider = new LocaleProvider();

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->any())
            ->method('getLocale')
            ->will($this->returnValue('en'));

        $this->assertSame(['en'], $localeProvider->getRequestLocales($requestMock));
    }

    /**
     * @covers \Netgen\BlockManager\Locale\LocaleProvider::getRequestLocales
     */
    public function testGetRequestLocalesWithEnabledLocales(): void
    {
        $localeProvider = new LocaleProvider(['en', 'hr']);

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->any())
            ->method('getLocale')
            ->will($this->returnValue('en'));

        $this->assertSame(['en'], $localeProvider->getRequestLocales($requestMock));
    }

    /**
     * @covers \Netgen\BlockManager\Locale\LocaleProvider::getRequestLocales
     */
    public function testGetRequestLocalesWithNonEnabledLocale(): void
    {
        $localeProvider = new LocaleProvider(['en', 'hr']);

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->any())
            ->method('getLocale')
            ->will($this->returnValue('de'));

        $this->assertSame([], $localeProvider->getRequestLocales($requestMock));
    }
}
