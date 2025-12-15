<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime;

use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Exception\Item\ItemException;
use Netgen\Layouts\Item\CmsItemInterface;
use Netgen\Layouts\Item\Registry\ValueTypeRegistry;
use Symfony\Component\Uid\Uuid;
use Throwable;

use function array_unshift;

final class HelpersRuntime
{
    public function __construct(
        private LayoutService $layoutService,
        private LayoutResolverService $layoutResolverService,
        private ValueTypeRegistry $valueTypeRegistry,
    ) {}

    /**
     * Returns the layout name for specified layout ID.
     */
    public function getLayoutName(string $layoutId): string
    {
        try {
            return $this->layoutService->loadLayout(Uuid::fromString($layoutId))->name;
        } catch (Throwable) {
            return '';
        }
    }

    /**
     * Returns the rule group for the specified rule ID.
     */
    public function getRuleGroup(string $ruleId): RuleGroup
    {
        $rule = $this->layoutResolverService->loadRule(Uuid::fromString($ruleId));

        return $this->layoutResolverService->loadRuleGroup($rule->ruleGroupId);
    }

    /**
     * Returns the rule group name for specified rule group ID.
     */
    public function getRuleGroupName(string $ruleGroupId): string
    {
        try {
            $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString($ruleGroupId));

            return $ruleGroup->name;
        } catch (Throwable) {
            return '';
        }
    }

    /**
     * Returns the the name of the value type that the specified item wraps.
     */
    public function getValueTypeName(CmsItemInterface $cmsItem): string
    {
        try {
            return $this->valueTypeRegistry->getValueType($cmsItem->valueType)->name;
        } catch (ItemException) {
            return '';
        }
    }

    /**
     * @return \Netgen\Layouts\API\Values\LayoutResolver\RuleGroup[]
     */
    public function getParentRuleGroups(Rule $rule): array
    {
        $group = $this->layoutResolverService->loadRuleGroup($rule->ruleGroupId);
        $parentGroups = [$group];

        $parentId = $group->parentId;

        while ($parentId !== null) {
            $group = $this->layoutResolverService->loadRuleGroup($parentId);
            array_unshift($parentGroups, $group);
            $parentId = $group->parentId;
        }

        return $parentGroups;
    }
}
