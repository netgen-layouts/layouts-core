<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\Layout;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Zone as APIZone;
use Netgen\BlockManager\Core\Values\Layout\Zone;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\Layout\ZoneParamConverter;
use PHPUnit\Framework\TestCase;

final class ZoneParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\Layout\ZoneParamConverter
     */
    private $paramConverter;

    public function setUp(): void
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        $this->paramConverter = new ZoneParamConverter($this->layoutServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Layout\ZoneParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Layout\ZoneParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName(): void
    {
        $this->assertSame(['layoutId', 'zoneIdentifier'], $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Layout\ZoneParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName(): void
    {
        $this->assertSame('zone', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Layout\ZoneParamConverter::getSupportedClass
     */
    public function testGetSupportedClass(): void
    {
        $this->assertSame(APIZone::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Layout\ZoneParamConverter::loadValue
     */
    public function testLoadValue(): void
    {
        $zone = new Zone();

        $this->layoutServiceMock
            ->expects($this->once())
            ->method('loadZone')
            ->with($this->equalTo(42), $this->equalTo('left'))
            ->will($this->returnValue($zone));

        $this->assertSame(
            $zone,
            $this->paramConverter->loadValue(
                [
                    'layoutId' => 42,
                    'zoneIdentifier' => 'left',
                    'status' => 'published',
                ]
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Layout\ZoneParamConverter::loadValue
     */
    public function testLoadValueDraft(): void
    {
        $zone = new Zone();

        $this->layoutServiceMock
            ->expects($this->once())
            ->method('loadZoneDraft')
            ->with($this->equalTo(42), $this->equalTo('left'))
            ->will($this->returnValue($zone));

        $this->assertSame(
            $zone,
            $this->paramConverter->loadValue(
                [
                    'layoutId' => 42,
                    'zoneIdentifier' => 'left',
                    'status' => 'draft',
                ]
            )
        );
    }
}
