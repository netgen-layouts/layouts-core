<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\LayoutResolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\TargetDraftParamConverter;
use Netgen\BlockManager\Core\Values\LayoutResolver\TargetDraft;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetDraft as APITargetDraft;
use PHPUnit\Framework\TestCase;

class TargetDraftParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutResolverServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\TargetDraftParamConverter
     */
    protected $paramConverter;

    public function setUp()
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->paramConverter = new TargetDraftParamConverter($this->layoutResolverServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\TargetDraftParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName()
    {
        $this->assertEquals(array('targetId'), $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\TargetDraftParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName()
    {
        $this->assertEquals('target', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\TargetDraftParamConverter::getSupportedClass
     */
    public function testGetSupportedClass()
    {
        $this->assertEquals(APITargetDraft::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\TargetDraftParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\TargetDraftParamConverter::loadValueObject
     */
    public function testLoadValueObject()
    {
        $layout = new TargetDraft();

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('loadTargetDraft')
            ->with($this->equalTo(42))
            ->will($this->returnValue($layout));

        $this->assertEquals($layout, $this->paramConverter->loadValueObject(array('targetId' => 42)));
    }
}
