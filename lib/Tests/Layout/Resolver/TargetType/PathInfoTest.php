<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\TargetType;

use Netgen\BlockManager\Layout\Resolver\TargetType\PathInfo;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;

class PathInfoTest extends TestCase
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetType\PathInfo
     */
    protected $targetType;

    public function setUp()
    {
        $request = Request::create('/the/answer');

        $this->requestStack = new RequestStack();
        $this->requestStack->push($request);

        $this->targetType = new PathInfo();
        $this->targetType->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\PathInfo::getIdentifier
     */
    public function testGetIdentifier()
    {
        self::assertEquals('path_info', $this->targetType->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\PathInfo::provideValue
     */
    public function testProvideValue()
    {
        self::assertEquals(
            '/the/answer',
            $this->targetType->provideValue()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\PathInfo::provideValue
     */
    public function testProvideValueWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        self::assertNull($this->targetType->provideValue());
    }
}
