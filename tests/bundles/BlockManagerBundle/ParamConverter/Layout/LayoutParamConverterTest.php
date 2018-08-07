<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\Layout;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Layout as APILayout;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\Layout\LayoutParamConverter;
use PHPUnit\Framework\TestCase;

final class LayoutParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\Layout\LayoutParamConverter
     */
    private $paramConverter;

    public function setUp(): void
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        $this->paramConverter = new LayoutParamConverter($this->layoutServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Layout\LayoutParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Layout\LayoutParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['layoutId'], $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Layout\LayoutParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('layout', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Layout\LayoutParamConverter::getSupportedClass
     */
    public function testGetSupportedClass(): void
    {
        self::assertSame(APILayout::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Layout\LayoutParamConverter::loadValue
     */
    public function testLoadValue(): void
    {
        $layout = new Layout();

        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayout')
            ->with(self::identicalTo(42))
            ->will(self::returnValue($layout));

        self::assertSame(
            $layout,
            $this->paramConverter->loadValue(
                [
                    'layoutId' => 42,
                    'status' => 'published',
                ]
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Layout\LayoutParamConverter::loadValue
     */
    public function testLoadValueArchive(): void
    {
        $layout = new Layout();

        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayoutArchive')
            ->with(self::identicalTo(42))
            ->will(self::returnValue($layout));

        self::assertSame(
            $layout,
            $this->paramConverter->loadValue(
                [
                    'layoutId' => 42,
                    'status' => 'archived',
                ]
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Layout\LayoutParamConverter::loadValue
     */
    public function testLoadValueDraft(): void
    {
        $layout = new Layout();

        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayoutDraft')
            ->with(self::identicalTo(42))
            ->will(self::returnValue($layout));

        self::assertSame(
            $layout,
            $this->paramConverter->loadValue(
                [
                    'layoutId' => 42,
                    'status' => 'draft',
                ]
            )
        );
    }
}
