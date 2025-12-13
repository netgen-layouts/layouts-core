<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\Layout;

use Netgen\Bundle\LayoutsBundle\ValueResolver\Layout\LayoutValueResolver;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(LayoutValueResolver::class)]
final class LayoutValueResolverTest extends TestCase
{
    private Stub&LayoutService $layoutServiceStub;

    private LayoutValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->layoutServiceStub = self::createStub(LayoutService::class);

        $this->valueResolver = new LayoutValueResolver($this->layoutServiceStub);
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

        $uuid = Uuid::v4();

        $this->layoutServiceStub
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

        $uuid = Uuid::v4();

        $this->layoutServiceStub
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

        $uuid = Uuid::v4();

        $this->layoutServiceStub
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
