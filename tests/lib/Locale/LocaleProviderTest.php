<?php

namespace Netgen\BlockManager\Tests\Locale;

use Netgen\BlockManager\Locale\LocaleProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class LocaleProviderTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Locale\LocaleProvider::__construct
     * @covers \Netgen\BlockManager\Locale\LocaleProvider::getAvailableLocales
     */
    public function testGetAvailableLocales()
    {
        $localeProvider = new LocaleProvider(array('en', 'hr'));

        $this->assertEquals(
            array(
                'en' => 'English',
                'hr' => 'Croatian',
            ),
            $localeProvider->getAvailableLocales()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Locale\LocaleProvider::__construct
     * @covers \Netgen\BlockManager\Locale\LocaleProvider::getAvailableLocales
     */
    public function testGetAvailableLocalesWithNonExistingLocales()
    {
        $localeProvider = new LocaleProvider(array('en', 'hr_NON_EXISTING'));

        $this->assertEquals(
            array(
                'en' => 'English',
            ),
            $localeProvider->getAvailableLocales()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Locale\LocaleProvider::__construct
     * @covers \Netgen\BlockManager\Locale\LocaleProvider::getAvailableLocales
     */
    public function testGetAvailableLocalesWithDefaultLocales()
    {
        $localeProvider = new LocaleProvider();

        $this->assertNotEmpty($localeProvider->getAvailableLocales());
    }

    /**
     * @covers \Netgen\BlockManager\Locale\LocaleProvider::getRequestLocales
     */
    public function testGetRequestLocales()
    {
        $localeProvider = new LocaleProvider();

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->any())
            ->method('getLocale')
            ->will($this->returnValue('en'));

        $this->assertEquals(array('en'), $localeProvider->getRequestLocales($requestMock));
    }

    /**
     * @covers \Netgen\BlockManager\Locale\LocaleProvider::getRequestLocales
     */
    public function testGetRequestLocalesWithEnabledLocales()
    {
        $localeProvider = new LocaleProvider(array('en', 'hr'));

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->any())
            ->method('getLocale')
            ->will($this->returnValue('en'));

        $this->assertEquals(array('en'), $localeProvider->getRequestLocales($requestMock));
    }

    /**
     * @covers \Netgen\BlockManager\Locale\LocaleProvider::getRequestLocales
     */
    public function testGetRequestLocalesWithNonEnabledLocale()
    {
        $localeProvider = new LocaleProvider(array('en', 'hr'));

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->any())
            ->method('getLocale')
            ->will($this->returnValue('de'));

        $this->assertEquals(array(), $localeProvider->getRequestLocales($requestMock));
    }
}
