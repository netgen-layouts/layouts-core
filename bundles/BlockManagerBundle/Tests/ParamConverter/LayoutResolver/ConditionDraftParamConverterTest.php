<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\LayoutResolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\ConditionDraftParamConverter;
use Netgen\BlockManager\Core\Values\LayoutResolver\ConditionDraft;
use Netgen\BlockManager\API\Values\LayoutResolver\ConditionDraft as APIConditionDraft;
use PHPUnit\Framework\TestCase;

class ConditionDraftParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutResolverServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\ConditionDraftParamConverter
     */
    protected $paramConverter;

    public function setUp()
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->paramConverter = new ConditionDraftParamConverter($this->layoutResolverServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\ConditionDraftParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName()
    {
        $this->assertEquals(array('conditionId'), $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\ConditionDraftParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName()
    {
        $this->assertEquals('condition', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\ConditionDraftParamConverter::getSupportedClass
     */
    public function testGetSupportedClass()
    {
        $this->assertEquals(APIConditionDraft::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\ConditionDraftParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\ConditionDraftParamConverter::loadValueObject
     */
    public function testLoadValueObject()
    {
        $layout = new ConditionDraft();

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('loadConditionDraft')
            ->with($this->equalTo(42))
            ->will($this->returnValue($layout));

        $this->assertEquals($layout, $this->paramConverter->loadValueObject(array('conditionId' => 42)));
    }
}
