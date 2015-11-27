<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\TargetBuilder\Builder;

use Netgen\BlockManager\LayoutResolver\Target\RoutePrefix as RoutePrefixTarget;
use Netgen\BlockManager\LayoutResolver\TargetBuilder\Builder\RoutePrefix;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class RoutePrefixTest extends \PHPUnit_Framework_TestCase
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
     * @covers \Netgen\BlockManager\LayoutResolver\TargetBuilder\Builder\RoutePrefix::buildTarget
     */
    public function testBuildTarget()
    {
        $targetBuilder = new RoutePrefix();
        $targetBuilder->setRequestStack($this->requestStack);

        self::assertEquals(new RoutePrefixTarget(array('my_cool_route')), $targetBuilder->buildTarget());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\TargetBuilder\Builder\RoutePrefix::buildTarget
     */
    public function testBuildTargetWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        $targetBuilder = new RoutePrefix();
        $targetBuilder->setRequestStack($this->requestStack);

        self::assertEquals(false, $targetBuilder->buildTarget());
    }
}
