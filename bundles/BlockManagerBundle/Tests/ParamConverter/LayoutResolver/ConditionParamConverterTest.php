<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\LayoutResolver;

use Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\ConditionParamConverter;
use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\API\Values\LayoutResolver\Condition as APICondition;
use Netgen\BlockManager\API\Service\LayoutResolverService;
use PHPUnit\Framework\TestCase;

class ConditionParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutResolverServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\ConditionParamConverter
     */
    protected $paramConverter;

    public function setUp()
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->paramConverter = new ConditionParamConverter($this->layoutResolverServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\ConditionParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName()
    {
        $this->assertEquals(array('conditionId'), $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\ConditionParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName()
    {
        $this->assertEquals('condition', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\ConditionParamConverter::getSupportedClass
     */
    public function testGetSupportedClass()
    {
        $this->assertEquals(APICondition::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\ConditionParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\ConditionParamConverter::loadValueObject
     */
    public function testLoadValueObject()
    {
        $condition = new Condition();

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('loadCondition')
            ->with($this->equalTo(42))
            ->will($this->returnValue($condition));

        $this->assertEquals($condition, $this->paramConverter->loadValueObject(array('conditionId' => 42)));
    }
}
