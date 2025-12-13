<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\Layout;

use Netgen\Bundle\LayoutsBundle\ValueResolver\Layout\ZoneValueResolver;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\API\Values\Layout\ZoneList;
use Netgen\Layouts\Exception\NotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(ZoneValueResolver::class)]
final class ZoneValueResolverTest extends TestCase
{
    private Stub&LayoutService $layoutServiceStub;

    private ZoneValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->layoutServiceStub = self::createStub(LayoutService::class);

        $this->valueResolver = new ZoneValueResolver($this->layoutServiceStub);
    }

    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['layoutId', 'zoneIdentifier'], $this->valueResolver->getSourceAttributeNames());
    }

    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('zone', $this->valueResolver->getDestinationAttributeName());
    }

    public function testGetSupportedClass(): void
    {
        self::assertSame(Zone::class, $this->valueResolver->getSupportedClass());
    }

    public function testLoadValue(): void
    {
        $zone = new Zone();
        $layout = Layout::fromArray(['zones' => ZoneList::fromArray(['left' => $zone])]);

        $uuid = Uuid::v4();

        $this->layoutServiceStub
            ->method('loadLayout')
            ->with(self::equalTo($uuid))
            ->willReturn($layout);

        self::assertSame(
            $zone,
            $this->valueResolver->loadValue(
                [
                    'layoutId' => $uuid->toString(),
                    'zoneIdentifier' => 'left',
                    'status' => 'published',
                ],
            ),
        );
    }

    public function testLoadValueDraft(): void
    {
        $zone = new Zone();
        $layout = Layout::fromArray(['zones' => ZoneList::fromArray(['left' => $zone])]);

        $uuid = Uuid::v4();

        $this->layoutServiceStub
            ->method('loadLayoutDraft')
            ->with(self::equalTo($uuid))
            ->willReturn($layout);

        self::assertSame(
            $zone,
            $this->valueResolver->loadValue(
                [
                    'layoutId' => $uuid->toString(),
                    'zoneIdentifier' => 'left',
                    'status' => 'draft',
                ],
            ),
        );
    }

    public function testLoadValueWithNonExistentZone(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find zone with identifier "left"');

        $zone = new Zone();
        $layout = Layout::fromArray(['zones' => ZoneList::fromArray(['right' => $zone])]);

        $uuid = Uuid::v4();

        $this->layoutServiceStub
            ->method('loadLayout')
            ->with(self::equalTo($uuid))
            ->willReturn($layout);

        $this->valueResolver->loadValue(
            [
                'layoutId' => $uuid->toString(),
                'zoneIdentifier' => 'left',
                'status' => 'published',
            ],
        );
    }
}
