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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(HelpersRuntime::class)]
final class HelpersRuntimeTest extends TestCase
{
    private HelpersRuntime $runtime;

    private MockObject&LayoutService $layoutServiceMock;

    private MockObject&LayoutResolverService $layoutResolverServiceMock;

    protected function setUp(): void
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->runtime = new HelpersRuntime(
            $this->layoutServiceMock,
            $this->layoutResolverServiceMock,
            new ValueTypeRegistry(
                [
                    'value' => ValueType::fromArray(['identifier' => 'value', 'name' => 'Value']),
                ],
            ),
        );
    }

    public function testGetLocaleName(): void
    {
        $localeName = $this->runtime->getLocaleName('hr_HR', 'hr_HR');

        self::assertSame('hrvatski (Hrvatska)', $localeName);
    }

    public function testGetLayoutName(): void
    {
        $uuid = Uuid::uuid4();

        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayout')
            ->with(self::equalTo($uuid))
            ->willReturn(Layout::fromArray(['name' => 'Test layout']));

        self::assertSame('Test layout', $this->runtime->getLayoutName($uuid->toString()));
    }

    public function testGetLayoutNameWithNonExistingLayout(): void
    {
        $uuid = Uuid::uuid4();

        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayout')
            ->with(self::equalTo($uuid))
            ->willThrowException(new NotFoundException('layout', $uuid->toString()));

        self::assertSame('', $this->runtime->getLayoutName($uuid->toString()));
    }

    public function testGetRuleGroup(): void
    {
        $ruleUuid = Uuid::uuid4();
        $groupUuid = Uuid::uuid4();

        $rule = Rule::fromArray(['id' => $ruleUuid, 'ruleGroupId' => $groupUuid]);
        $ruleGroup = RuleGroup::fromArray(['id' => $groupUuid]);

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadRule')
            ->with(self::equalTo($ruleUuid))
            ->willReturn($rule);

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadRuleGroup')
            ->with(self::equalTo($groupUuid))
            ->willReturn($ruleGroup);

        self::assertSame($ruleGroup, $this->runtime->getRuleGroup($ruleUuid->toString()));
    }

    public function testGetRuleGroupName(): void
    {
        $uuid = Uuid::uuid4();

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadRuleGroup')
            ->with(self::equalTo($uuid))
            ->willReturn(RuleGroup::fromArray(['name' => 'Test rule group']));

        self::assertSame('Test rule group', $this->runtime->getRuleGroupName($uuid->toString()));
    }

    public function testGetRuleGroupNameWithNonExistingRuleGroup(): void
    {
        $uuid = Uuid::uuid4();

        $this->layoutResolverServiceMock
            ->expects(self::once())
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
