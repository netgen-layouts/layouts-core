<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver\RuleGroupValueResolver;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(RuleGroupValueResolver::class)]
final class RuleGroupValueResolverTest extends TestCase
{
    private MockObject $layoutResolverServiceMock;

    private RuleGroupValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->valueResolver = new RuleGroupValueResolver($this->layoutResolverServiceMock);
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

        $uuid = Uuid::uuid4();

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadRuleGroup')
            ->with(self::equalTo($uuid))
            ->willReturn($ruleGroup);

        self::assertSame(
            $ruleGroup,
            $this->valueResolver->loadValue(
                [
                    'ruleGroupId' => $uuid->toString(),
                    'status' => 'published',
                ],
            ),
        );
    }

    public function testLoadValueArchive(): void
    {
        $ruleGroup = new RuleGroup();

        $uuid = Uuid::uuid4();

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadRuleGroupArchive')
            ->with(self::equalTo($uuid))
            ->willReturn($ruleGroup);

        self::assertSame(
            $ruleGroup,
            $this->valueResolver->loadValue(
                [
                    'ruleGroupId' => $uuid->toString(),
                    'status' => 'archived',
                ],
            ),
        );
    }

    public function testLoadValueDraft(): void
    {
        $ruleGroup = new RuleGroup();

        $uuid = Uuid::uuid4();

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadRuleGroupDraft')
            ->with(self::equalTo($uuid))
            ->willReturn($ruleGroup);

        self::assertSame(
            $ruleGroup,
            $this->valueResolver->loadValue(
                [
                    'ruleGroupId' => $uuid->toString(),
                    'status' => 'draft',
                ],
            ),
        );
    }
}
