<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ParamConverter\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleGroupConditionParamConverter;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupCondition;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class RuleGroupConditionParamConverterTest extends TestCase
{
    private MockObject $layoutResolverServiceMock;

    private RuleGroupConditionParamConverter $paramConverter;

    protected function setUp(): void
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->paramConverter = new RuleGroupConditionParamConverter($this->layoutResolverServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleGroupConditionParamConverter::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleGroupConditionParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['conditionId'], $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleGroupConditionParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('condition', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleGroupConditionParamConverter::getSupportedClass
     */
    public function testGetSupportedClass(): void
    {
        self::assertSame(RuleGroupCondition::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleGroupConditionParamConverter::loadValue
     */
    public function testLoadValue(): void
    {
        $condition = new RuleGroupCondition();

        $uuid = Uuid::uuid4();

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadRuleGroupCondition')
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
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleGroupConditionParamConverter::loadValue
     */
    public function testLoadValueDraft(): void
    {
        $condition = new RuleGroupCondition();

        $uuid = Uuid::uuid4();

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadRuleGroupConditionDraft')
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
