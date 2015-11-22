<?php

namespace Netgen\BlockManager\LayoutResolver\Tests\TargetBuilder;

use Netgen\BlockManager\LayoutResolver\Target;
use Netgen\BlockManager\LayoutResolver\TargetBuilder\Route;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit_Framework_TestCase;

class RouteTest extends PHPUnit_Framework_TestCase
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
     * @covers \Netgen\BlockManager\LayoutResolver\TargetBuilder\Route::getTargetIdentifier
     */
    public function testGetTargetIdentifier()
    {
        $targetBuilder = new Route();

        self::assertEquals('route', $targetBuilder->getTargetIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\TargetBuilder\Route::buildTarget
     */
    public function testBuildTarget()
    {
        $targetBuilder = new Route();
        $targetBuilder->setRequestStack($this->requestStack);

        self::assertEquals(new Target('route', array('my_cool_route')), $targetBuilder->buildTarget());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\TargetBuilder\Route::buildTarget
     */
    public function testBuildTargetWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        $targetBuilder = new Route();
        $targetBuilder->setRequestStack($this->requestStack);

        self::assertEquals(false, $targetBuilder->buildTarget());
    }
}
