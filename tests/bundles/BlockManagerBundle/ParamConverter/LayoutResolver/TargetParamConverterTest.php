<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\LayoutResolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\LayoutResolver\Target as APITarget;
use Netgen\BlockManager\Core\Values\LayoutResolver\Target;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\TargetParamConverter;
use PHPUnit\Framework\TestCase;

class TargetParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutResolverServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\TargetParamConverter
     */
    protected $paramConverter;

    public function setUp()
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->paramConverter = new TargetParamConverter($this->layoutResolverServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\TargetParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\TargetParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName()
    {
        $this->assertEquals(array('targetId'), $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\TargetParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName()
    {
        $this->assertEquals('target', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\TargetParamConverter::getSupportedClass
     */
    public function testGetSupportedClass()
    {
        $this->assertEquals(APITarget::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\TargetParamConverter::loadValueObject
     */
    public function testLoadValueObject()
    {
        $target = new Target();

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('loadTarget')
            ->with($this->equalTo(42))
            ->will($this->returnValue($target));

        $this->assertEquals(
            $target,
            $this->paramConverter->loadValueObject(
                array(
                    'targetId' => 42,
                    'published' => true,
                )
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\TargetParamConverter::loadValueObject
     */
    public function testLoadValueObjectDraft()
    {
        $target = new Target();

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('loadTargetDraft')
            ->with($this->equalTo(42))
            ->will($this->returnValue($target));

        $this->assertEquals(
            $target,
            $this->paramConverter->loadValueObject(
                array(
                    'targetId' => 42,
                    'published' => false,
                )
            )
        );
    }
}
