<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig\Runtime;

use Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\HelpersRuntime;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Item\Registry\ValueTypeRegistry;
use Netgen\Layouts\Item\ValueType\ValueType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(HelpersRuntime::class)]
final class HelpersRuntimeTest extends TestCase
{
    private HelpersRuntime $runtime;

    private Stub&LayoutService $layoutServiceStub;

    private Stub&LayoutResolverService $layoutResolverServiceStub;

    protected function setUp(): void
    {
        $this->layoutServiceStub = self::createStub(LayoutService::class);
        $this->layoutResolverServiceStub = self::createStub(LayoutResolverService::class);

        $this->runtime = new HelpersRuntime(
            $this->layoutServiceStub,
            $this->layoutResolverServiceStub,
            new ValueTypeRegistry(
                [
                    'value' => ValueType::fromArray(['identifier' => 'value', 'name' => 'Value']),
                ],
            ),
        );
    }

    public function testGetLayoutName(): void
    {
        $uuid = Uuid::v7();

        $this->layoutServiceStub
            ->method('loadLayout')
            ->with(self::equalTo($uuid))
            ->willReturn(Layout::fromArray(['name' => 'Test layout']));

        self::assertSame('Test layout', $this->runtime->getLayoutName($uuid->toString()));
    }

    public function testGetLayoutNameWithNonExistingLayout(): void
    {
        $uuid = Uuid::v7();

        $this->layoutServiceStub
            ->method('loadLayout')
            ->with(self::equalTo($uuid))
            ->willThrowException(new NotFoundException('layout', $uuid->toString()));

        self::assertSame('', $this->runtime->getLayoutName($uuid->toString()));
    }

    public function testGetRuleGroup(): void
    {
        $ruleUuid = Uuid::v7();
        $groupUuid = Uuid::v7();

        $rule = Rule::fromArray(['id' => $ruleUuid, 'ruleGroupId' => $groupUuid]);
        $ruleGroup = RuleGroup::fromArray(['id' => $groupUuid]);

        $this->layoutResolverServiceStub
            ->method('loadRule')
            ->with(self::equalTo($ruleUuid))
            ->willReturn($rule);

        $this->layoutResolverServiceStub
            ->method('loadRuleGroup')
            ->with(self::equalTo($groupUuid))
            ->willReturn($ruleGroup);

        self::assertSame($ruleGroup, $this->runtime->getRuleGroup($ruleUuid->toString()));
    }

    public function testGetRuleGroupName(): void
    {
        $uuid = Uuid::v7();

        $this->layoutResolverServiceStub
            ->method('loadRuleGroup')
            ->with(self::equalTo($uuid))
            ->willReturn(RuleGroup::fromArray(['name' => 'Test rule group']));

        self::assertSame('Test rule group', $this->runtime->getRuleGroupName($uuid->toString()));
    }

    public function testGetRuleGroupNameWithNonExistingRuleGroup(): void
    {
        $uuid = Uuid::v7();

        $this->layoutResolverServiceStub
            ->method('loadRuleGroup')
            ->with(self::equalTo($uuid))
            ->willThrowException(new NotFoundException('rule group', $uuid->toString()));

        self::assertSame('', $this->runtime->getRuleGroupName($uuid->toString()));
    }

    public function testGetValueTypeName(): void
    {
        $cmsItem = CmsItem::fromArray(['valueType' => 'value']);

        self::assertSame('Value', $this->runtime->getValueTypeName($cmsItem));
    }

    public function testGetValueTypeNameWithNonExistingLayout(): void
    {
        $cmsItem = CmsItem::fromArray(['valueType' => 'non_existing']);

        self::assertSame('', $this->runtime->getValueTypeName($cmsItem));
    }
}
