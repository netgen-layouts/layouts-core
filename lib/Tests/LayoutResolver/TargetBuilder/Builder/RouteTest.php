<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\TargetBuilder\Builder;

use Netgen\BlockManager\LayoutResolver\Target\Route as RouteTarget;
use Netgen\BlockManager\LayoutResolver\TargetBuilder\Builder\Route;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    use RequestStackAwareTrait;

    /**
     * Sets up the route target builder tests.
     */
    public function setUp()
    {
        $request = Request::create('/');
        $request->attributes->set('_route', 'my_cool_route');

        $requestStack = new RequestStack();
        $requestStack->push($request);
        $this->setRequestStack($requestStack);
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\TargetBuilder\Builder\Route::buildTarget
     */
    public function testBuildTarget()
    {
        $targetBuilder = new Route();
        $targetBuilder->setRequestStack($this->requestStack);

        self::assertEquals(new RouteTarget(array('my_cool_route')), $targetBuilder->buildTarget());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\TargetBuilder\Builder\Route::buildTarget
     */
    public function testBuildTargetWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        $targetBuilder = new Route();
        $targetBuilder->setRequestStack($this->requestStack);

        self::assertFalse($targetBuilder->buildTarget());
    }
}
