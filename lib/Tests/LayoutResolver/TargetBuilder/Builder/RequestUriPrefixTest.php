<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\TargetBuilder\Builder;

use Netgen\BlockManager\LayoutResolver\Target\RequestUriPrefix as RequestUriPrefixTarget;
use Netgen\BlockManager\LayoutResolver\TargetBuilder\Builder\RequestUriPrefix;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class RequestUriPrefixTest extends \PHPUnit_Framework_TestCase
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
     * @covers \Netgen\BlockManager\LayoutResolver\TargetBuilder\Builder\RequestUriPrefix::buildTarget
     */
    public function testBuildTarget()
    {
        $targetBuilder = new RequestUriPrefix();
        $targetBuilder->setRequestStack($this->requestStack);

        self::assertEquals(new RequestUriPrefixTarget(array('/the/answer?a=42')), $targetBuilder->buildTarget());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\TargetBuilder\Builder\RequestUriPrefix::buildTarget
     */
    public function testBuildTargetWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        $targetBuilder = new RequestUriPrefix();
        $targetBuilder->setRequestStack($this->requestStack);

        self::assertFalse($targetBuilder->buildTarget());
    }
}
