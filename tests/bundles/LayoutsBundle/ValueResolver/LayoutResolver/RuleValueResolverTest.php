<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver\RuleValueResolver;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class RuleValueResolverTest extends TestCase
{
    private MockObject $layoutResolverServiceMock;

    private RuleValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->valueResolver = new RuleValueResolver($this->layoutResolverServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver\RuleValueResolver::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver\RuleValueResolver::getSourceAttributeNames
     */
    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['ruleId'], $this->valueResolver->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver\RuleValueResolver::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('rule', $this->valueResolver->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver\RuleValueResolver::getSupportedClass
     */
    public function testGetSupportedClass(): void
    {
        self::assertSame(Rule::class, $this->valueResolver->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver\RuleValueResolver::loadValue
     */
    public function testLoadValue(): void
    {
        $rule = new Rule();

        $uuid = Uuid::uuid4();

        $this->layoutResolverServiceMock
            ->expects(self::once())
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

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver\RuleValueResolver::loadValue
     */
    public function testLoadValueArchive(): void
    {
        $rule = new Rule();

        $uuid = Uuid::uuid4();

        $this->layoutResolverServiceMock
            ->expects(self::once())
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

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver\RuleValueResolver::loadValue
     */
    public function testLoadValueDraft(): void
    {
        $rule = new Rule();

        $uuid = Uuid::uuid4();

        $this->layoutResolverServiceMock
            ->expects(self::once())
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
