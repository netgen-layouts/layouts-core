<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ParamConverter\Layout;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\Bundle\LayoutsBundle\ParamConverter\Layout\ZoneParamConverter;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\Exception\NotFoundException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class ZoneParamConverterTest extends TestCase
{
    private MockObject $layoutServiceMock;

    private ZoneParamConverter $paramConverter;

    protected function setUp(): void
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

        $uuid = Uuid::uuid4();

        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayout')
            ->with(self::equalTo($uuid))
            ->willReturn($layout);

        self::assertSame(
            $zone,
            $this->paramConverter->loadValue(
                [
                    'layoutId' => $uuid->toString(),
                    'zoneIdentifier' => 'left',
                    'status' => 'published',
                ],
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Layout\ZoneParamConverter::loadValue
     */
    public function testLoadValueDraft(): void
    {
        $zone = new Zone();
        $layout = Layout::fromArray(['zones' => new ArrayCollection(['left' => $zone])]);

        $uuid = Uuid::uuid4();

        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayoutDraft')
            ->with(self::equalTo($uuid))
            ->willReturn($layout);

        self::assertSame(
            $zone,
            $this->paramConverter->loadValue(
                [
                    'layoutId' => $uuid->toString(),
                    'zoneIdentifier' => 'left',
                    'status' => 'draft',
                ],
            ),
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

        $uuid = Uuid::uuid4();

        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayout')
            ->with(self::equalTo($uuid))
            ->willReturn($layout);

        $this->paramConverter->loadValue(
            [
                'layoutId' => $uuid->toString(),
                'zoneIdentifier' => 'left',
                'status' => 'published',
            ],
        );
    }
}
