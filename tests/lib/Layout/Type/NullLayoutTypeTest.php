<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Type;

use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Layout\Type\NullLayoutType;
use PHPUnit\Framework\TestCase;

final class NullLayoutTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Type\NullLayoutType
     */
    private $layoutType;

    public function setUp(): void
    {
        $this->layoutType = new NullLayoutType('type');
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\NullLayoutType::__construct
     * @covers \Netgen\BlockManager\Layout\Type\NullLayoutType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        $this->assertEquals('type', $this->layoutType->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\NullLayoutType::isEnabled
     */
    public function testIsEnabled(): void
    {
        $this->assertTrue($this->layoutType->isEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\NullLayoutType::getName
     */
    public function testGetName(): void
    {
        $this->assertEquals('Invalid layout type', $this->layoutType->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\NullLayoutType::getIcon
     */
    public function testGetIcon(): void
    {
        $this->assertEquals('', $this->layoutType->getIcon());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\NullLayoutType::getZones
     */
    public function testGetZones(): void
    {
        $this->assertEquals([], $this->layoutType->getZones());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\NullLayoutType::getZoneIdentifiers
     */
    public function testGetZoneIdentifiers(): void
    {
        $this->assertEquals([], $this->layoutType->getZoneIdentifiers());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\NullLayoutType::hasZone
     */
    public function testHasZone(): void
    {
        $this->assertFalse($this->layoutType->hasZone('left'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\NullLayoutType::getZone
     * @expectedException \Netgen\BlockManager\Exception\Layout\LayoutTypeException
     * @expectedExceptionMessage Zone "left" does not exist in "type" layout type.
     */
    public function testGetZone(): void
    {
        $this->layoutType->getZone('left');
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\NullLayoutType::isBlockAllowedInZone
     */
    public function testIsBlockAllowedInZone(): void
    {
        $this->assertTrue($this->layoutType->isBlockAllowedInZone(new BlockDefinition(['identifier' => 'title']), 'left'));
    }
}
