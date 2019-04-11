<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ParamConverter\Layout;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\Bundle\LayoutsBundle\ParamConverter\Layout\ZoneParamConverter;
use PHPUnit\Framework\TestCase;

final class ZoneParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutServiceMock;

    /**
     * @var \Netgen\Bundle\LayoutsBundle\ParamConverter\Layout\ZoneParamConverter
     */
    private $paramConverter;

    public function setUp(): void
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        $this->paramConverter = new ZoneParamConverter($this->layoutServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Layout\ZoneParamConverter::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Layout\ZoneParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['layoutId', 'zoneIdentifier'], $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Layout\ZoneParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('zone', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Layout\ZoneParamConverter::getSupportedClass
     */
    public function testGetSupportedClass(): void
    {
        self::assertSame(Zone::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Layout\ZoneParamConverter::loadValue
     */
    public function testLoadValue(): void
    {
        $zone = new Zone();

        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadZone')
            ->with(self::identicalTo(42), self::identicalTo('left'))
            ->willReturn($zone);

        self::assertSame(
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
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Layout\ZoneParamConverter::loadValue
     */
    public function testLoadValueDraft(): void
    {
        $zone = new Zone();

        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadZoneDraft')
            ->with(self::identicalTo(42), self::identicalTo('left'))
            ->willReturn($zone);

        self::assertSame(
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
