<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\Layout;

use Netgen\Bundle\LayoutsBundle\ValueResolver\Layout\LayoutValueResolver;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(LayoutValueResolver::class)]
final class LayoutValueResolverTest extends TestCase
{
    private MockObject&LayoutService $layoutServiceMock;

    private LayoutValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        $this->valueResolver = new LayoutValueResolver($this->layoutServiceMock);
    }

    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['layoutId'], $this->valueResolver->getSourceAttributeNames());
    }

    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('layout', $this->valueResolver->getDestinationAttributeName());
    }

    public function testGetSupportedClass(): void
    {
        self::assertSame(Layout::class, $this->valueResolver->getSupportedClass());
    }

    public function testLoadValue(): void
    {
        $layout = new Layout();

        $uuid = Uuid::uuid4();

        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayout')
            ->with(self::equalTo($uuid))
            ->willReturn($layout);

        self::assertSame(
            $layout,
            $this->valueResolver->loadValue(
                [
                    'layoutId' => $uuid->toString(),
                    'status' => 'published',
                ],
            ),
        );
    }

    public function testLoadValueArchive(): void
    {
        $layout = new Layout();

        $uuid = Uuid::uuid4();

        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayoutArchive')
            ->with(self::equalTo($uuid))
            ->willReturn($layout);

        self::assertSame(
            $layout,
            $this->valueResolver->loadValue(
                [
                    'layoutId' => $uuid->toString(),
                    'status' => 'archived',
                ],
            ),
        );
    }

    public function testLoadValueDraft(): void
    {
        $layout = new Layout();

        $uuid = Uuid::uuid4();

        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayoutDraft')
            ->with(self::equalTo($uuid))
            ->willReturn($layout);

        self::assertSame(
            $layout,
            $this->valueResolver->loadValue(
                [
                    'layoutId' => $uuid->toString(),
                    'status' => 'draft',
                ],
            ),
        );
    }
}
