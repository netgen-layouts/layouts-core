<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\TargetValueProvider;

use Netgen\BlockManager\Layout\Resolver\TargetValueProvider\RequestUri;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class RequestUriTest extends \PHPUnit_Framework_TestCase
{
    use RequestStackAwareTrait;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetValueProvider\RequestUri
     */
    protected $targetValueProvider;

    public function setUp()
    {
        $request = Request::create('/the/answer', 'GET', array('a' => 42));

        $requestStack = new RequestStack();
        $requestStack->push($request);
        $this->setRequestStack($requestStack);

        $this->targetValueProvider = new RequestUri();
        $this->targetValueProvider->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetValueProvider\RequestUri::provideValue
     */
    public function testProvideValue()
    {
        self::assertEquals(
            '/the/answer?a=42',
            $this->targetValueProvider->provideValue()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetValueProvider\RequestUri::provideValue
     */
    public function testProvideValueWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        self::assertNull($this->targetValueProvider->provideValue());
    }
}
