<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\LayoutResolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\LayoutResolver\Condition as APICondition;
use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\ConditionParamConverter;
use PHPUnit\Framework\TestCase;

final class ConditionParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutResolverServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\ConditionParamConverter
     */
    private $paramConverter;

    public function setUp()
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->paramConverter = new ConditionParamConverter($this->layoutResolverServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\ConditionParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\ConditionParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName()
    {
        $this->assertEquals(['conditionId'], $this->paramConverter->getSourceAttributeNames());
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
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\ConditionParamConverter::loadValue
     */
    public function testLoadValue()
    {
        $condition = new Condition();

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('loadCondition')
            ->with($this->equalTo(42))
            ->will($this->returnValue($condition));

        $this->assertEquals(
            $condition,
            $this->paramConverter->loadValue(
                [
                    'conditionId' => 42,
                    'status' => 'published',
                ]
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\ConditionParamConverter::loadValue
     */
    public function testLoadValueDraft()
    {
        $condition = new Condition();

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('loadConditionDraft')
            ->with($this->equalTo(42))
            ->will($this->returnValue($condition));

        $this->assertEquals(
            $condition,
            $this->paramConverter->loadValue(
                [
                    'conditionId' => 42,
                    'status' => 'draft',
                ]
            )
        );
    }
}
