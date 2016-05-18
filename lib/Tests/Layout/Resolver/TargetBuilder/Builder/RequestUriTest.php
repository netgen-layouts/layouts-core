<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\TargetBuilder\Builder;

use Netgen\BlockManager\Layout\Resolver\Target\RequestUri as RequestUriTarget;
use Netgen\BlockManager\Layout\Resolver\TargetBuilder\Builder\RequestUri;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class RequestUriTest extends \PHPUnit_Framework_TestCase
{
    use RequestStackAwareTrait;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetBuilder\Builder\RequestUri
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

        $this->targetBuilder = new RequestUri();
        $this->targetBuilder->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetBuilder\Builder\RequestUri::buildTarget
     */
    public function testBuildTarget()
    {
        self::assertEquals(new RequestUriTarget(array('/the/answer?a=42')), $this->targetBuilder->buildTarget());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetBuilder\Builder\RequestUri::buildTarget
     */
    public function testBuildTargetWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        self::assertFalse($this->targetBuilder->buildTarget());
    }
}
