<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver;

use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use function array_filter;
use function array_values;
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

        foreach ($this->innerResolveRules($currentRequest, $enabledConditions) as $resolvedRule) {
            if ($resolvedRule->getLayout() instanceof Layout) {
                return $resolvedRule;
            }
        }

        return null;
    }

    public function resolveRules(?Request $request = null, array $enabledConditions = []): array
    {
        if ($request === null) {
            trigger_deprecation('netgen/layouts-core', '1.3', 'Calling "LayoutResolverInterface::resolveRule" method with no request is deprecated. In 2.0, "$request" argument will become required.');
        }

        $currentRequest = $request ?? $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return [];
        }

        return array_values(
            array_filter(
                $this->innerResolveRules($currentRequest, $enabledConditions),
                static fn (Rule $rule): bool => $rule->getLayout() instanceof Layout
            )
        );
    }

    public function matches(Rule $rule, Request $request, array $enabledConditions = []): bool
    {
        foreach ($rule->getConditions() as $condition) {
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

    /**
     * @param string[] $enabledConditions
     *
     * @return \Netgen\Layouts\API\Values\LayoutResolver\Rule[]
     */
    private function innerResolveRules(Request $request, array $enabledConditions = []): array
    {
        $resolvedRules = [];

        foreach ($this->targetTypeRegistry->getTargetTypes() as $targetType) {
            $targetValue = $targetType->provideValue($request);
            if ($targetValue === null) {
                continue;
            }

            $matchedRules = $this->layoutResolverService->matchRules(
                $this->layoutResolverService->loadRuleGroup(Uuid::fromString(RuleGroup::ROOT_UUID)),
                $targetType::getType(),
                $targetValue
            );

            foreach ($matchedRules as $matchedRule) {
                if (!$matchedRule->isEnabled() || !$this->matches($matchedRule, $request, $enabledConditions)) {
                    continue;
                }

                $resolvedRules[] = $matchedRule;
            }
        }

        usort(
            $resolvedRules,
            static fn (Rule $a, Rule $b): int => $b->getPriority() <=> $a->getPriority()
        );

        return $resolvedRules;
    }
}
