<?php

namespace Netgen\BlockManager\Tests\Locale\Context;

use Netgen\BlockManager\Locale\Context\RequestLocaleContext;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestLocaleContextTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Locale\Context\RequestLocaleContext
     */
    protected $localeContext;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    public function setUp()
    {
        $this->requestStack = new RequestStack();

        $this->localeContext = new RequestLocaleContext($this->requestStack);
    }

    /**
     * @covers \Netgen\BlockManager\Locale\Context\RequestLocaleContext::__construct
     * @covers \Netgen\BlockManager\Locale\Context\RequestLocaleContext::getLocaleCodes
     */
    public function testGetLocaleCodes()
    {
        $request = Request::create('/');
        $request->attributes->set('_locale', 'en');
        $this->requestStack->push($request);

        $this->assertEquals(array('en'), $this->localeContext->getLocaleCodes());
    }

    /**
     * @covers \Netgen\BlockManager\Locale\Context\RequestLocaleContext::getLocaleCodes
     * @expectedException \Netgen\BlockManager\Exception\Locale\LocaleException
     * @expectedExceptionMessage No locales available in the current context.
     */
    public function testGetLocaleCodesThrowsLocaleExceptionWithNoRequest()
    {
        $this->localeContext->getLocaleCodes();
    }
}
