<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ParamConverter\Layout;

use Netgen\Bundle\LayoutsBundle\ParamConverter\Layout\LayoutParamConverter;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class LayoutParamConverterTest extends TestCase
{
    private MockObject $layoutServiceMock;

    private LayoutParamConverter $paramConverter;

    protected function setUp(): void
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        $this->paramConverter = new LayoutParamConverter($this->layoutServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Layout\LayoutParamConverter::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Layout\LayoutParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['layoutId'], $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Layout\LayoutParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('layout', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Layout\LayoutParamConverter::getSupportedClass
     */
    public function testGetSupportedClass(): void
    {
        self::assertSame(Layout::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Layout\LayoutParamConverter::loadValue
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
            $this->paramConverter->loadValue(
                [
                    'layoutId' => $uuid->toString(),
                    'status' => 'published',
                ],
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Layout\LayoutParamConverter::loadValue
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
            $this->paramConverter->loadValue(
                [
                    'layoutId' => $uuid->toString(),
                    'status' => 'archived',
                ],
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Layout\LayoutParamConverter::loadValue
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
            $this->paramConverter->loadValue(
                [
                    'layoutId' => $uuid->toString(),
                    'status' => 'draft',
                ],
            ),
        );
    }
}
