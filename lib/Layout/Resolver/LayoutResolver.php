<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver;

use Generator;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\ConditionList;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;

use function count;
use function in_array;
use function usort;

final class LayoutResolver implements LayoutResolverInterface
{
    public function __construct(
        private LayoutResolverService $layoutResolverService,
        private TargetTypeRegistry $targetTypeRegistry,
    ) {}

    public function resolveRule(Request $request, array $enabledConditions = []): ?Rule
    {
        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString(RuleGroup::ROOT_UUID));

        foreach ($this->innerResolveRules($ruleGroup, $request, $enabledConditions) as $resolvedRule) {
            return $resolvedRule;
        }

        return null;
    }

    public function resolveRules(Request $request, array $enabledConditions = []): array
    {
        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString(RuleGroup::ROOT_UUID));

        return [...$this->innerResolveRules($ruleGroup, $request, $enabledConditions)];
    }

    /**
     * @param string[] $enabledConditions
     *
     * @return \Generator<int, \Netgen\Layouts\API\Values\LayoutResolver\Rule>
     */
    private function innerResolveRules(RuleGroup $ruleGroup, Request $request, array $enabledConditions = []): Generator
    {
        $resolvedGroups = $this->layoutResolverService->loadRuleGroups($ruleGroup)->filter(
            fn (RuleGroup $ruleGroup): bool => $ruleGroup->enabled
                && $this->conditionsMatch($ruleGroup->conditions, $request, $enabledConditions),
        );

        $matches = [...$resolvedGroups, ...$this->resolveGroupRules($ruleGroup, $request, $enabledConditions)];
        usort($matches, static fn ($a, $b): int => $b->priority <=> $a->priority);

        foreach ($matches as $match) {
            /** @var \Netgen\Layouts\API\Values\LayoutResolver\Rule|\Netgen\Layouts\API\Values\LayoutResolver\RuleGroup $match */
            if ($match instanceof RuleGroup) {
                yield from $this->innerResolveRules($match, $request, $enabledConditions);

                continue;
            }

            if (!$match->layout instanceof Layout) {
                continue;
            }

            yield $match;
        }
    }

    /**
     * @param string[] $enabledConditions
     *
     * @return \Generator<int, \Netgen\Layouts\API\Values\LayoutResolver\Rule>
     */
    private function resolveGroupRules(RuleGroup $ruleGroup, Request $request, array $enabledConditions = []): Generator
    {
        foreach ($this->targetTypeRegistry->getTargetTypes() as $targetType) {
            $targetValue = $targetType->provideValue($request);
            if ($targetValue === null) {
                continue;
            }

            yield from $this->layoutResolverService->matchRules($ruleGroup, $targetType::getType(), $targetValue)->filter(
                fn (Rule $rule): bool => $rule->enabled
                    && $this->conditionsMatch($rule->conditions, $request, $enabledConditions),
            );
        }
    }

    /**
     * @param string[] $enabledConditions
     */
    private function conditionsMatch(ConditionList $conditions, Request $request, array $enabledConditions = []): bool
    {
        foreach ($conditions as $condition) {
            $conditionType = $condition->conditionType;

            if (count($enabledConditions) > 0 && !in_array($conditionType::getType(), $enabledConditions, true)) {
                continue;
            }

            if (!$conditionType->matches($request, $condition->value)) {
                return false;
            }
        }

        return true;
    }
}
