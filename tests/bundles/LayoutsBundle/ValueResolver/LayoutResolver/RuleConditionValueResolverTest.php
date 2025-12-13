<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver\RuleConditionValueResolver;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(RuleConditionValueResolver::class)]
final class RuleConditionValueResolverTest extends TestCase
{
    private Stub&LayoutResolverService $layoutResolverServiceStub;

    private RuleConditionValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->layoutResolverServiceStub = self::createStub(LayoutResolverService::class);

        $this->valueResolver = new RuleConditionValueResolver($this->layoutResolverServiceStub);
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
        self::assertSame(RuleCondition::class, $this->valueResolver->getSupportedClass());
    }

    public function testLoadValue(): void
    {
        $condition = new RuleCondition();

        $uuid = Uuid::v4();

        $this->layoutResolverServiceStub
            ->method('loadRuleCondition')
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
        $condition = new RuleCondition();

        $uuid = Uuid::v4();

        $this->layoutResolverServiceStub
            ->method('loadRuleConditionDraft')
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
