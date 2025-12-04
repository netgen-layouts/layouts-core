<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver\RuleValueResolver;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(RuleValueResolver::class)]
final class RuleValueResolverTest extends TestCase
{
    private Stub&LayoutResolverService $layoutResolverServiceStub;

    private RuleValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->layoutResolverServiceStub = self::createStub(LayoutResolverService::class);

        $this->valueResolver = new RuleValueResolver($this->layoutResolverServiceStub);
    }

    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['ruleId'], $this->valueResolver->getSourceAttributeNames());
    }

    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('rule', $this->valueResolver->getDestinationAttributeName());
    }

    public function testGetSupportedClass(): void
    {
        self::assertSame(Rule::class, $this->valueResolver->getSupportedClass());
    }

    public function testLoadValue(): void
    {
        $rule = new Rule();

        $uuid = Uuid::uuid4();

        $this->layoutResolverServiceStub
            ->method('loadRule')
            ->with(self::equalTo($uuid))
            ->willReturn($rule);

        self::assertSame(
            $rule,
            $this->valueResolver->loadValue(
                [
                    'ruleId' => $uuid->toString(),
                    'status' => 'published',
                ],
            ),
        );
    }

    public function testLoadValueArchive(): void
    {
        $rule = new Rule();

        $uuid = Uuid::uuid4();

        $this->layoutResolverServiceStub
            ->method('loadRuleArchive')
            ->with(self::equalTo($uuid))
            ->willReturn($rule);

        self::assertSame(
            $rule,
            $this->valueResolver->loadValue(
                [
                    'ruleId' => $uuid->toString(),
                    'status' => 'archived',
                ],
            ),
        );
    }

    public function testLoadValueDraft(): void
    {
        $rule = new Rule();

        $uuid = Uuid::uuid4();

        $this->layoutResolverServiceStub
            ->method('loadRuleDraft')
            ->with(self::equalTo($uuid))
            ->willReturn($rule);

        self::assertSame(
            $rule,
            $this->valueResolver->loadValue(
                [
                    'ruleId' => $uuid->toString(),
                    'status' => 'draft',
                ],
            ),
        );
    }
}
