<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\Page;

use Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\LayoutDraftParamConverter;
use Netgen\BlockManager\Core\Values\Page\LayoutDraft;
use Netgen\BlockManager\API\Values\Page\LayoutDraft as APILayoutDraft;
use Netgen\BlockManager\API\Service\LayoutService;
use PHPUnit\Framework\TestCase;

class LayoutDraftParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\LayoutDraftParamConverter
     */
    protected $paramConverter;

    public function setUp()
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        $this->paramConverter = new LayoutDraftParamConverter($this->layoutServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\LayoutDraftParamConverter::getSourceAttributeName
     */
    public function testGetSourceAttributeName()
    {
        self::assertEquals(array('layoutId'), $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\LayoutDraftParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName()
    {
        self::assertEquals('layout', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\LayoutDraftParamConverter::getSupportedClass
     */
    public function testGetSupportedClass()
    {
        self::assertEquals(APILayoutDraft::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\LayoutDraftParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\LayoutDraftParamConverter::loadValueObject
     */
    public function testLoadValueObject()
    {
        $layout = new LayoutDraft();

        $this->layoutServiceMock
            ->expects($this->once())
            ->method('loadLayoutDraft')
            ->with($this->equalTo(42))
            ->will($this->returnValue($layout));

        self::assertEquals($layout, $this->paramConverter->loadValueObject(array('layoutId' => 42)));
    }
}
