<?php

namespace Netgen\BlockManager\Tests\Locale\Context;

use Netgen\BlockManager\Locale\Context\ChainedLocaleContext;
use Netgen\BlockManager\Tests\Locale\Stubs\LocaleContext;
use PHPUnit\Framework\TestCase;
use stdClass;

class ChainedLocaleContextTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Locale\Context\ChainedLocaleContext::__construct
     * @expectedException \Netgen\BlockManager\Exception\InvalidInterfaceException
     * @expectedExceptionMessage Locale context "stdClass" needs to implement "Netgen\BlockManager\Locale\LocaleContextInterface" interface.
     */
    public function testConstructorThrowsInvalidInterfaceException()
    {
        new ChainedLocaleContext(
            array(
                new stdClass(),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Locale\Context\ChainedLocaleContext::__construct
     * @covers \Netgen\BlockManager\Locale\Context\ChainedLocaleContext::getLocaleCodes
     */
    public function testGetLocaleCodes()
    {
        $localeContext = new ChainedLocaleContext(
            array(
                new LocaleContext(array('en')),
                new LocaleContext(array('hr')),
            )
        );

        $this->assertEquals(array('en'), $localeContext->getLocaleCodes());
    }

    /**
     * @covers \Netgen\BlockManager\Locale\Context\ChainedLocaleContext::getLocaleCodes
     */
    public function testGetLocaleCodesWithFallback()
    {
        $localeContext = new ChainedLocaleContext(
            array(
                new LocaleContext(),
                new LocaleContext(array('hr')),
            )
        );

        $this->assertEquals(array('hr'), $localeContext->getLocaleCodes());
    }

    /**
     * @covers \Netgen\BlockManager\Locale\Context\ChainedLocaleContext::getLocaleCodes
     * @expectedException \Netgen\BlockManager\Exception\Locale\LocaleException
     * @expectedExceptionMessage No locales available in the current context.
     */
    public function testGetLocaleCodesThrowsLocaleExceptionWithNoFallback()
    {
        $localeContext = new ChainedLocaleContext();

        $localeContext->getLocaleCodes();
    }
}
