<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver\RuleGroupConditionValueResolver;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupCondition;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class RuleGroupConditionValueResolverTest extends TestCase
{
    private MockObject $layoutResolverServiceMock;

    private RuleGroupConditionValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->valueResolver = new RuleGroupConditionValueResolver($this->layoutResolverServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver\RuleGroupConditionValueResolver::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver\RuleGroupConditionValueResolver::getSourceAttributeNames
     */
    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['conditionId'], $this->valueResolver->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver\RuleGroupConditionValueResolver::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('condition', $this->valueResolver->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver\RuleGroupConditionValueResolver::getSupportedClass
     */
    public function testGetSupportedClass(): void
    {
        self::assertSame(RuleGroupCondition::class, $this->valueResolver->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver\RuleGroupConditionValueResolver::loadValue
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
            $this->valueResolver->loadValue(
                [
                    'conditionId' => $uuid->toString(),
                    'status' => 'published',
                ],
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver\RuleGroupConditionValueResolver::loadValue
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
            $this->valueResolver->loadValue(
                [
                    'conditionId' => $uuid->toString(),
                    'status' => 'draft',
                ],
            ),
        );
    }
}
