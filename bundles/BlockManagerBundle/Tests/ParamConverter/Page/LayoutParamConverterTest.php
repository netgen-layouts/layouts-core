<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\Page;

use Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\LayoutParamConverter;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\API\Values\Page\Layout as APILayout;
use Netgen\BlockManager\API\Service\LayoutService;

class LayoutParamConverterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\LayoutParamConverter
     */
    protected $paramConverter;

    public function setUp()
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        $this->paramConverter = new LayoutParamConverter($this->layoutServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\LayoutParamConverter::getSourceAttributeName
     */
    public function testGetSourceAttributeName()
    {
        self::assertEquals('layoutId', $this->paramConverter->getSourceAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\LayoutParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName()
    {
        self::assertEquals('layout', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\LayoutParamConverter::getSupportedClass
     */
    public function testGetSupportedClass()
    {
        self::assertEquals(APILayout::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\LayoutParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\LayoutParamConverter::loadValueObject
     */
    public function testLoadValueObject()
    {
        $layout = new Layout();

        $this->layoutServiceMock
            ->expects($this->once())
            ->method('loadLayout')
            ->with($this->equalTo(42))
            ->will($this->returnValue($layout));

        self::assertEquals($layout, $this->paramConverter->loadValueObject(42));
    }
}
