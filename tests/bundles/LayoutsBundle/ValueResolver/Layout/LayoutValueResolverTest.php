<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\Layout;

use Netgen\Bundle\LayoutsBundle\ValueResolver\Layout\LayoutValueResolver;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class LayoutValueResolverTest extends TestCase
{
    private MockObject $layoutServiceMock;

    private LayoutValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        $this->valueResolver = new LayoutValueResolver($this->layoutServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\Layout\LayoutValueResolver::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\Layout\LayoutValueResolver::getSourceAttributeNames
     */
    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['layoutId'], $this->valueResolver->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\Layout\LayoutValueResolver::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('layout', $this->valueResolver->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\Layout\LayoutValueResolver::getSupportedClass
     */
    public function testGetSupportedClass(): void
    {
        self::assertSame(Layout::class, $this->valueResolver->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\Layout\LayoutValueResolver::loadValue
     */
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

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\Layout\LayoutValueResolver::loadValue
     */
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

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\Layout\LayoutValueResolver::loadValue
     */
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
