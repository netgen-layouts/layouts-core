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
use Symfony\Component\HttpFoundation\RequestStack;

use function count;
use function in_array;
use function trigger_deprecation;
use function usort;

final class LayoutResolver implements LayoutResolverInterface
{
    private LayoutResolverService $layoutResolverService;

    private TargetTypeRegistry $targetTypeRegistry;

    private RequestStack $requestStack;

    public function __construct(
        LayoutResolverService $layoutResolverService,
        TargetTypeRegistry $targetTypeRegistry,
        RequestStack $requestStack
    ) {
        $this->layoutResolverService = $layoutResolverService;
        $this->targetTypeRegistry = $targetTypeRegistry;
        $this->requestStack = $requestStack;
    }

    public function resolveRule(?Request $request = null, array $enabledConditions = []): ?Rule
    {
        if ($request === null) {
            trigger_deprecation('netgen/layouts-core', '1.3', 'Calling "LayoutResolverInterface::resolveRule" method with no request is deprecated. In 2.0, "$request" argument will become required.');
        }

        $currentRequest = $request ?? $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return null;
        }

        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString(RuleGroup::ROOT_UUID));

        foreach ($this->innerResolveRules($ruleGroup, $currentRequest, $enabledConditions) as $resolvedRule) {
            return $resolvedRule;
        }

        return null;
    }

    public function resolveRules(?Request $request = null, array $enabledConditions = []): array
    {
        if ($request === null) {
            trigger_deprecation('netgen/layouts-core', '1.3', 'Calling "LayoutResolverInterface::resolveRules" method with no request is deprecated. In 2.0, "$request" argument will become required.');
        }

        $currentRequest = $request ?? $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return [];
        }

        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString(RuleGroup::ROOT_UUID));

        return [...$this->innerResolveRules($ruleGroup, $currentRequest, $enabledConditions)];
    }

    public function matches(Rule $rule, Request $request, array $enabledConditions = []): bool
    {
        return $this->conditionsMatch($rule->getConditions(), $request, $enabledConditions);
    }

    /**
     * @param string[] $enabledConditions
     *
     * @return \Generator<int, \Netgen\Layouts\API\Values\LayoutResolver\Rule>
     */
    private function innerResolveRules(RuleGroup $ruleGroup, Request $request, array $enabledConditions = []): Generator
    {
        $resolvedGroups = $this->layoutResolverService->loadRuleGroups($ruleGroup)->filter(
            fn (RuleGroup $ruleGroup): bool => $ruleGroup->isEnabled()
                && $this->conditionsMatch($ruleGroup->getConditions(), $request, $enabledConditions),
        );

        $matches = [...$resolvedGroups, ...$this->resolveGroupRules($ruleGroup, $request, $enabledConditions)];
        usort($matches, static fn ($a, $b): int => $b->getPriority() <=> $a->getPriority());

        foreach ($matches as $match) {
            /** @var \Netgen\Layouts\API\Values\LayoutResolver\Rule|\Netgen\Layouts\API\Values\LayoutResolver\RuleGroup $match */
            if ($match instanceof RuleGroup) {
                yield from $this->innerResolveRules($match, $request, $enabledConditions);

                continue;
            }

            if (!$match->getLayout() instanceof Layout) {
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
                fn (Rule $rule): bool => $rule->isEnabled()
                    && $this->conditionsMatch($rule->getConditions(), $request, $enabledConditions),
            );
        }
    }

    /**
     * @param string[] $enabledConditions
     */
    private function conditionsMatch(ConditionList $conditions, Request $request, array $enabledConditions = []): bool
    {
        foreach ($conditions as $condition) {
            $conditionType = $condition->getConditionType();

            if (count($enabledConditions) > 0 && !in_array($conditionType::getType(), $enabledConditions, true)) {
                continue;
            }

            if (!$conditionType->matches($request, $condition->getValue())) {
                return false;
            }
        }

        return true;
    }
}
