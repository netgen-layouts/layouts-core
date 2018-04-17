<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\LayoutResolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\LayoutResolver\Target as APITarget;
use Netgen\BlockManager\Core\Values\LayoutResolver\Target;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\TargetParamConverter;
use PHPUnit\Framework\TestCase;

final class TargetParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutResolverServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\TargetParamConverter
     */
    private $paramConverter;

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
        $this->assertEquals(['targetId'], $this->paramConverter->getSourceAttributeNames());
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
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\TargetParamConverter::loadValue
     */
    public function testLoadValue()
    {
        $target = new Target();

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('loadTarget')
            ->with($this->equalTo(42))
            ->will($this->returnValue($target));

        $this->assertEquals(
            $target,
            $this->paramConverter->loadValue(
                [
                    'targetId' => 42,
                    'published' => true,
                ]
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\TargetParamConverter::loadValue
     */
    public function testLoadValueDraft()
    {
        $target = new Target();

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('loadTargetDraft')
            ->with($this->equalTo(42))
            ->will($this->returnValue($target));

        $this->assertEquals(
            $target,
            $this->paramConverter->loadValue(
                [
                    'targetId' => 42,
                    'published' => false,
                ]
            )
        );
    }
}
