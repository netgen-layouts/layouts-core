<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\TargetBuilder\Builder;

use Netgen\BlockManager\Layout\Resolver\Target\PathInfo as PathInfoTarget;
use Netgen\BlockManager\Layout\Resolver\TargetBuilder\Builder\PathInfo;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class PathInfoTest extends \PHPUnit_Framework_TestCase
{
    use RequestStackAwareTrait;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetBuilder\Builder\PathInfo
     */
    protected $targetBuilder;

    /**
     * Sets up the route target builder tests.
     */
    public function setUp()
    {
        $request = Request::create('/the/answer');

        $requestStack = new RequestStack();
        $requestStack->push($request);
        $this->setRequestStack($requestStack);

        $this->targetBuilder = new PathInfo();
        $this->targetBuilder->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetBuilder\Builder\PathInfo::buildTarget
     */
    public function testBuildTarget()
    {
        self::assertEquals(new PathInfoTarget(array('/the/answer')), $this->targetBuilder->buildTarget());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetBuilder\Builder\PathInfo::buildTarget
     */
    public function testBuildTargetWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        self::assertFalse($this->targetBuilder->buildTarget());
    }
}
