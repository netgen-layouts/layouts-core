<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\Layout;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Layout as APILayout;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\Layout\LayoutParamConverter;
use PHPUnit\Framework\TestCase;

class LayoutParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\Layout\LayoutParamConverter
     */
    protected $paramConverter;

    public function setUp()
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        $this->paramConverter = new LayoutParamConverter($this->layoutServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Layout\LayoutParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Layout\LayoutParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName()
    {
        $this->assertEquals(array('layoutId'), $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Layout\LayoutParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName()
    {
        $this->assertEquals('layout', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Layout\LayoutParamConverter::getSupportedClass
     */
    public function testGetSupportedClass()
    {
        $this->assertEquals(APILayout::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Layout\LayoutParamConverter::loadValueObject
     */
    public function testLoadValueObject()
    {
        $layout = new Layout();

        $this->layoutServiceMock
            ->expects($this->once())
            ->method('loadLayout')
            ->with($this->equalTo(42))
            ->will($this->returnValue($layout));

        $this->assertEquals(
            $layout,
            $this->paramConverter->loadValueObject(array('layoutId' => 42, 'published' => true))
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Layout\LayoutParamConverter::loadValueObject
     */
    public function testLoadValueObjectDraft()
    {
        $layout = new Layout();

        $this->layoutServiceMock
            ->expects($this->once())
            ->method('loadLayoutDraft')
            ->with($this->equalTo(42))
            ->will($this->returnValue($layout));

        $this->assertEquals(
            $layout,
            $this->paramConverter->loadValueObject(
                array(
                    'layoutId' => 42,
                    'published' => false,
                )
            )
        );
    }
}