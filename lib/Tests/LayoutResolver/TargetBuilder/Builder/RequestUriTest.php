<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\TargetBuilder\Builder;

use Netgen\BlockManager\LayoutResolver\Target\RequestUri as RequestUriTarget;
use Netgen\BlockManager\LayoutResolver\TargetBuilder\Builder\RequestUri;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class RequestUriTest extends \PHPUnit_Framework_TestCase
{
    use RequestStackAwareTrait;

    /**
     * Sets up the route target builder tests.
     */
    public function setUp()
    {
        $request = Request::create('/the/answer', 'GET', array('a' => 42));

        $requestStack = new RequestStack();
        $requestStack->push($request);
        $this->setRequestStack($requestStack);
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\TargetBuilder\Builder\RequestUri::buildTarget
     */
    public function testBuildTarget()
    {
        $targetBuilder = new RequestUri();
        $targetBuilder->setRequestStack($this->requestStack);

        self::assertEquals(new RequestUriTarget(array('/the/answer?a=42')), $targetBuilder->buildTarget());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\TargetBuilder\Builder\RequestUri::buildTarget
     */
    public function testBuildTargetWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        $targetBuilder = new RequestUri();
        $targetBuilder->setRequestStack($this->requestStack);

        self::assertFalse($targetBuilder->buildTarget());
    }
}
