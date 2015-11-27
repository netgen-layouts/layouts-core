<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\TargetBuilder\Builder;

use Netgen\BlockManager\LayoutResolver\Target;
use Netgen\BlockManager\LayoutResolver\TargetBuilder\Builder\PathInfoPrefix;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class PathInfoPrefixTest extends \PHPUnit_Framework_TestCase
{
    use RequestStackAwareTrait;

    /**
     * Sets up the route target builder tests.
     */
    public function setUp()
    {
        $request = Request::create('/the/answer');

        $requestStack = new RequestStack();
        $requestStack->push($request);
        $this->setRequestStack($requestStack);
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\TargetBuilder\Builder\PathInfoPrefix::getTargetIdentifier
     */
    public function testGetTargetIdentifier()
    {
        $targetBuilder = new PathInfoPrefix();

        self::assertEquals('path_info_prefix', $targetBuilder->getTargetIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\TargetBuilder\Builder\PathInfoPrefix::buildTarget
     */
    public function testBuildTarget()
    {
        $targetBuilder = new PathInfoPrefix();
        $targetBuilder->setRequestStack($this->requestStack);

        self::assertEquals(new Target('path_info_prefix', array('/the/answer')), $targetBuilder->buildTarget());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\TargetBuilder\Builder\PathInfoPrefix::buildTarget
     */
    public function testBuildTargetWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        $targetBuilder = new PathInfoPrefix();
        $targetBuilder->setRequestStack($this->requestStack);

        self::assertEquals(false, $targetBuilder->buildTarget());
    }
}
