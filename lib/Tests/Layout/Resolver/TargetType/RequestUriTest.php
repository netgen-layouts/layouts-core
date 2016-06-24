<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\TargetType;

use Netgen\BlockManager\Layout\Resolver\TargetType\RequestUri;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;

class RequestUriTest extends TestCase
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetType\RequestUri
     */
    protected $targetType;

    public function setUp()
    {
        $request = Request::create('/the/answer', 'GET', array('a' => 42));

        $this->requestStack = new RequestStack();
        $this->requestStack->push($request);

        $this->targetType = new RequestUri();
        $this->targetType->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\RequestUri::getIdentifier
     */
    public function testGetIdentifier()
    {
        self::assertEquals('request_uri', $this->targetType->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\RequestUri::provideValue
     */
    public function testProvideValue()
    {
        self::assertEquals(
            '/the/answer?a=42',
            $this->targetType->provideValue()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\RequestUri::provideValue
     */
    public function testProvideValueWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        self::assertNull($this->targetType->provideValue());
    }
}
