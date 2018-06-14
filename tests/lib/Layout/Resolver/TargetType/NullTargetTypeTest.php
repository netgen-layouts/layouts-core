<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Resolver\TargetType;

use Netgen\BlockManager\Layout\Resolver\TargetType\NullTargetType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class NullTargetTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetType\NullTargetType
     */
    private $targetType;

    public function setUp(): void
    {
        $this->targetType = new NullTargetType('type');
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\NullTargetType::__construct
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\NullTargetType::getType
     */
    public function testGetType(): void
    {
        $this->assertEquals('type', $this->targetType->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\NullTargetType::getConstraints
     */
    public function testGetConstraints(): void
    {
        $this->assertEquals([], $this->targetType->getConstraints());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\NullTargetType::provideValue
     */
    public function testProvideValue(): void
    {
        $this->assertNull($this->targetType->provideValue(Request::create('')));
    }
}
