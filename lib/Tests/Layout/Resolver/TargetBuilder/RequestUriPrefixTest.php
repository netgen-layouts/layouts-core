<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\TargetBuilder;

use Netgen\BlockManager\Layout\Resolver\Target;
use Netgen\BlockManager\Layout\Resolver\TargetBuilder\RequestUriPrefix;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class RequestUriPrefixTest extends \PHPUnit_Framework_TestCase
{
    use RequestStackAwareTrait;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetBuilder\RequestUriPrefix
     */
    protected $targetBuilder;

    /**
     * Sets up the route target builder tests.
     */
    public function setUp()
    {
        $request = Request::create('/the/answer', 'GET', array('a' => 42));

        $requestStack = new RequestStack();
        $requestStack->push($request);
        $this->setRequestStack($requestStack);

        $this->targetBuilder = new RequestUriPrefix();
        $this->targetBuilder->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetBuilder\RequestUriPrefix::buildTarget
     */
    public function testBuildTarget()
    {
        self::assertEquals(new Target('request_uri_prefix', array('/the/answer?a=42')), $this->targetBuilder->buildTarget());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetBuilder\RequestUriPrefix::buildTarget
     */
    public function testBuildTargetWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        self::assertNull($this->targetBuilder->buildTarget());
    }
}
