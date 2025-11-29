<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver\RuleValueResolver;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(RuleValueResolver::class)]
final class RuleValueResolverTest extends TestCase
{
    private MockObject&LayoutResolverService $layoutResolverServiceMock;

    private RuleValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->valueResolver = new RuleValueResolver($this->layoutResolverServiceMock);
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

        $this->layoutResolverServiceMock
            ->expects($this->once())
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

        $this->layoutResolverServiceMock
            ->expects($this->once())
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

        $this->layoutResolverServiceMock
            ->expects($this->once())
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
