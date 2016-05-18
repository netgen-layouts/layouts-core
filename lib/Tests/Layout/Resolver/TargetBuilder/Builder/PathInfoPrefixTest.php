<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\TargetBuilder\Builder;

use Netgen\BlockManager\Layout\Resolver\Target\PathInfoPrefix as PathInfoPrefixTarget;
use Netgen\BlockManager\Layout\Resolver\TargetBuilder\Builder\PathInfoPrefix;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class PathInfoPrefixTest extends \PHPUnit_Framework_TestCase
{
    use RequestStackAwareTrait;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetBuilder\Builder\PathInfoPrefix
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

        $this->targetBuilder = new PathInfoPrefix();
        $this->targetBuilder->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetBuilder\Builder\PathInfoPrefix::buildTarget
     */
    public function testBuildTarget()
    {
        self::assertEquals(new PathInfoPrefixTarget(array('/the/answer')), $this->targetBuilder->buildTarget());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetBuilder\Builder\PathInfoPrefix::buildTarget
     */
    public function testBuildTargetWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        self::assertFalse($this->targetBuilder->buildTarget());
    }
}
