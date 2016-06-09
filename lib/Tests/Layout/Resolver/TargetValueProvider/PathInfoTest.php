<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\TargetValueProvider;

use Netgen\BlockManager\Layout\Resolver\TargetValueProvider\PathInfo;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;

class PathInfoTest extends TestCase
{
    use RequestStackAwareTrait;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetValueProvider\PathInfo
     */
    protected $targetValueProvider;

    public function setUp()
    {
        $request = Request::create('/the/answer');

        $requestStack = new RequestStack();
        $requestStack->push($request);
        $this->setRequestStack($requestStack);

        $this->targetValueProvider = new PathInfo();
        $this->targetValueProvider->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetValueProvider\PathInfo::provideValue
     */
    public function testProvideValue()
    {
        self::assertEquals(
            '/the/answer',
            $this->targetValueProvider->provideValue()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetValueProvider\PathInfo::provideValue
     */
    public function testProvideValueWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        self::assertNull($this->targetValueProvider->provideValue());
    }
}
