<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ParamConverter\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleConditionParamConverter;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class RuleConditionParamConverterTest extends TestCase
{
    private MockObject $layoutResolverServiceMock;

    private RuleConditionParamConverter $paramConverter;

    protected function setUp(): void
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->paramConverter = new RuleConditionParamConverter($this->layoutResolverServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleConditionParamConverter::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleConditionParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['conditionId'], $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleConditionParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('condition', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleConditionParamConverter::getSupportedClass
     */
    public function testGetSupportedClass(): void
    {
        self::assertSame(RuleCondition::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleConditionParamConverter::loadValue
     */
    public function testLoadValue(): void
    {
        $condition = new RuleCondition();

        $uuid = Uuid::uuid4();

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadRuleCondition')
            ->with(self::equalTo($uuid))
            ->willReturn($condition);

        self::assertSame(
            $condition,
            $this->paramConverter->loadValue(
                [
                    'conditionId' => $uuid->toString(),
                    'status' => 'published',
                ],
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleConditionParamConverter::loadValue
     */
    public function testLoadValueDraft(): void
    {
        $condition = new RuleCondition();

        $uuid = Uuid::uuid4();

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadRuleConditionDraft')
            ->with(self::equalTo($uuid))
            ->willReturn($condition);

        self::assertSame(
            $condition,
            $this->paramConverter->loadValue(
                [
                    'conditionId' => $uuid->toString(),
                    'status' => 'draft',
                ],
            ),
        );
    }
}
