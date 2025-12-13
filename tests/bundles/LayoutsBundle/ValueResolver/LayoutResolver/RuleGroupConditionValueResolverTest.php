<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver\RuleGroupConditionValueResolver;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupCondition;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(RuleGroupConditionValueResolver::class)]
final class RuleGroupConditionValueResolverTest extends TestCase
{
    private Stub&LayoutResolverService $layoutResolverServiceStub;

    private RuleGroupConditionValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->layoutResolverServiceStub = self::createStub(LayoutResolverService::class);

        $this->valueResolver = new RuleGroupConditionValueResolver($this->layoutResolverServiceStub);
    }

    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['conditionId'], $this->valueResolver->getSourceAttributeNames());
    }

    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('condition', $this->valueResolver->getDestinationAttributeName());
    }

    public function testGetSupportedClass(): void
    {
        self::assertSame(RuleGroupCondition::class, $this->valueResolver->getSupportedClass());
    }

    public function testLoadValue(): void
    {
        $condition = new RuleGroupCondition();

        $uuid = Uuid::v4();

        $this->layoutResolverServiceStub
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

    public function testLoadValueDraft(): void
    {
        $condition = new RuleGroupCondition();

        $uuid = Uuid::v4();

        $this->layoutResolverServiceStub
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
