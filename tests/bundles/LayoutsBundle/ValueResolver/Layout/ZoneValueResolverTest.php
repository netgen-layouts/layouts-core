<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\Layout;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\Bundle\LayoutsBundle\ValueResolver\Layout\ZoneValueResolver;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\Exception\NotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(ZoneValueResolver::class)]
final class ZoneValueResolverTest extends TestCase
{
    private MockObject&LayoutService $layoutServiceMock;

    private ZoneValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        $this->valueResolver = new ZoneValueResolver($this->layoutServiceMock);
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
        $layout = Layout::fromArray(['zones' => new ArrayCollection(['left' => $zone])]);

        $uuid = Uuid::uuid4();

        $this->layoutServiceMock
            ->expects(self::once())
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
        $layout = Layout::fromArray(['zones' => new ArrayCollection(['left' => $zone])]);

        $uuid = Uuid::uuid4();

        $this->layoutServiceMock
            ->expects(self::once())
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
        $layout = Layout::fromArray(['zones' => new ArrayCollection(['right' => $zone])]);

        $uuid = Uuid::uuid4();

        $this->layoutServiceMock
            ->expects(self::once())
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
