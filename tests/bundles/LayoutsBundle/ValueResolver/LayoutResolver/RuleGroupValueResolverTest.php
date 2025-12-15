<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver\RuleGroupValueResolver;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\API\Values\Status;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(RuleGroupValueResolver::class)]
final class RuleGroupValueResolverTest extends TestCase
{
    private Stub&LayoutResolverService $layoutResolverServiceStub;

    private RuleGroupValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->layoutResolverServiceStub = self::createStub(LayoutResolverService::class);

        $this->valueResolver = new RuleGroupValueResolver($this->layoutResolverServiceStub);
    }

    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['ruleGroupId'], $this->valueResolver->getSourceAttributeNames());
    }

    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('ruleGroup', $this->valueResolver->getDestinationAttributeName());
    }

    public function testGetSupportedClass(): void
    {
        self::assertSame(RuleGroup::class, $this->valueResolver->getSupportedClass());
    }

    public function testLoadValue(): void
    {
        $ruleGroup = new RuleGroup();

        $uuid = Uuid::v4();

        $this->layoutResolverServiceStub
            ->method('loadRuleGroup')
            ->with(self::equalTo($uuid))
            ->willReturn($ruleGroup);

        self::assertSame(
            $ruleGroup,
            $this->valueResolver->loadValue(
                [
                    'ruleGroupId' => $uuid->toString(),
                    'status' => Status::Published,
                ],
            ),
        );
    }

    public function testLoadValueArchive(): void
    {
        $ruleGroup = new RuleGroup();

        $uuid = Uuid::v4();

        $this->layoutResolverServiceStub
            ->method('loadRuleGroupArchive')
            ->with(self::equalTo($uuid))
            ->willReturn($ruleGroup);

        self::assertSame(
            $ruleGroup,
            $this->valueResolver->loadValue(
                [
                    'ruleGroupId' => $uuid->toString(),
                    'status' => Status::Archived,
                ],
            ),
        );
    }

    public function testLoadValueDraft(): void
    {
        $ruleGroup = new RuleGroup();

        $uuid = Uuid::v4();

        $this->layoutResolverServiceStub
            ->method('loadRuleGroupDraft')
            ->with(self::equalTo($uuid))
            ->willReturn($ruleGroup);

        self::assertSame(
            $ruleGroup,
            $this->valueResolver->loadValue(
                [
                    'ruleGroupId' => $uuid->toString(),
                    'status' => Status::Draft,
                ],
            ),
        );
    }
}
