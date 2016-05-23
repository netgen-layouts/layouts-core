<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\TargetBuilder;

use Netgen\BlockManager\Layout\Resolver\Target;
use Netgen\BlockManager\Layout\Resolver\TargetBuilder\PathInfoPrefix;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class PathInfoPrefixTest extends \PHPUnit_Framework_TestCase
{
    use RequestStackAwareTrait;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetBuilder\PathInfoPrefix
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
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetBuilder\PathInfoPrefix::buildTarget
     */
    public function testBuildTarget()
    {
        self::assertEquals(
            new Target(array('identifier' => 'path_info_prefix', 'values' => array('/the/answer'))),
            $this->targetBuilder->buildTarget()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetBuilder\PathInfoPrefix::buildTarget
     */
    public function testBuildTargetWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        self::assertNull($this->targetBuilder->buildTarget());
    }
}
