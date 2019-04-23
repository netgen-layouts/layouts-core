<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ParamConverter\Layout;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\Bundle\LayoutsBundle\ParamConverter\Layout\ZoneParamConverter;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\Exception\NotFoundException;
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
        $layout = Layout::fromArray(['zones' => new ArrayCollection(['left' => $zone])]);

        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayout')
            ->with(self::identicalTo(42))
            ->willReturn($layout);

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
        $layout = Layout::fromArray(['zones' => new ArrayCollection(['left' => $zone])]);

        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayoutDraft')
            ->with(self::identicalTo(42))
            ->willReturn($layout);

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

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Layout\ZoneParamConverter::loadValue
     */
    public function testLoadValueWithNonExistentZone(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find zone with identifier "left"');

        $zone = new Zone();
        $layout = Layout::fromArray(['zones' => new ArrayCollection(['right' => $zone])]);

        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayout')
            ->with(self::identicalTo(42))
            ->willReturn($layout);

        $this->paramConverter->loadValue(
            [
                'layoutId' => 42,
                'zoneIdentifier' => 'left',
                'status' => 'published',
            ]
        );
    }
}
