<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\TargetType;

use Netgen\BlockManager\Layout\Resolver\TargetType\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetType\Route
     */
    protected $targetType;

    public function setUp()
    {
        $request = Request::create('/');
        $request->attributes->set('_route', 'my_cool_route');

        $this->requestStack = new RequestStack();
        $this->requestStack->push($request);

        $this->targetType = new Route();
        $this->targetType->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\Route::getIdentifier
     */
    public function testGetIdentifier()
    {
        self::assertEquals('route', $this->targetType->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\Route::provideValue
     */
    public function testProvideValue()
    {
        self::assertEquals(
            'my_cool_route',
            $this->targetType->provideValue()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\Route::provideValue
     */
    public function testProvideValueWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        self::assertNull($this->targetType->provideValue());
    }
}
