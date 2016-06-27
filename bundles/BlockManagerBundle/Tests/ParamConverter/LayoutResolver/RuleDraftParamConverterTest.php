<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\LayoutResolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\RuleDraftParamConverter;
use Netgen\BlockManager\Core\Values\LayoutResolver\RuleDraft;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft as APIRuleDraft;
use PHPUnit\Framework\TestCase;

class RuleDraftParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutResolverServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\RuleDraftParamConverter
     */
    protected $paramConverter;

    public function setUp()
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->paramConverter = new RuleDraftParamConverter($this->layoutResolverServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\RuleDraftParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName()
    {
        self::assertEquals(array('ruleId'), $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\RuleDraftParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName()
    {
        self::assertEquals('rule', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\RuleDraftParamConverter::getSupportedClass
     */
    public function testGetSupportedClass()
    {
        self::assertEquals(APIRuleDraft::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\RuleDraftParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\RuleDraftParamConverter::loadValueObject
     */
    public function testLoadValueObject()
    {
        $layout = new RuleDraft();

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('loadRuleDraft')
            ->with($this->equalTo(42))
            ->will($this->returnValue($layout));

        self::assertEquals($layout, $this->paramConverter->loadValueObject(array('ruleId' => 42)));
    }
}
