<?php

namespace Netgen\BlockManager\Tests\Locale;

use Netgen\BlockManager\Locale\LocaleProvider;
use PHPUnit\Framework\TestCase;

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
}
