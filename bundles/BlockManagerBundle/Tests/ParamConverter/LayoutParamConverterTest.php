<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter;

use Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutParamConverter;
use Netgen\BlockManager\Core\Values\Page\Layout;

class LayoutParamConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutParamConverter::getSourceAttributeName
     */
    public function testGetSourceAttributeName()
    {
        $layoutService = $this->getMock('Netgen\BlockManager\API\Service\LayoutService');
        $layoutParamConverter = new LayoutParamConverter($layoutService);

        self::assertEquals('layoutId', $layoutParamConverter->getSourceAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName()
    {
        $layoutService = $this->getMock('Netgen\BlockManager\API\Service\LayoutService');
        $layoutParamConverter = new LayoutParamConverter($layoutService);

        self::assertEquals('layout', $layoutParamConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutParamConverter::getSupportedClass
     */
    public function testGetSupportedClass()
    {
        $layoutService = $this->getMock('Netgen\BlockManager\API\Service\LayoutService');
        $layoutParamConverter = new LayoutParamConverter($layoutService);

        self::assertEquals('Netgen\BlockManager\API\Values\Page\Layout', $layoutParamConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutParamConverter::loadValueObject
     */
    public function testLoadValueObject()
    {
        $layout = new Layout();

        $layoutService = $this->getMock('Netgen\BlockManager\API\Service\LayoutService');
        $layoutService
            ->expects($this->once())
            ->method('loadLayout')
            ->with($this->equalTo(42))
            ->will($this->returnValue($layout));

        $layoutParamConverter = new LayoutParamConverter($layoutService);

        self::assertEquals($layout, $layoutParamConverter->loadValueObject(42));
    }
}
