<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Resolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class LayoutResolver implements LayoutResolverInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    private $layoutResolverService;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface
     */
    private $targetTypeRegistry;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    public function __construct(
        LayoutResolverService $layoutResolverService,
        TargetTypeRegistryInterface $targetTypeRegistry,
        RequestStack $requestStack
    ) {
        $this->layoutResolverService = $layoutResolverService;
        $this->targetTypeRegistry = $targetTypeRegistry;
        $this->requestStack = $requestStack;
    }

    public function resolveRule(?Request $request = null, array $enabledConditions = []): ?Rule
    {
        $resolvedRules = $this->innerResolveRules($request, $enabledConditions);

        foreach ($resolvedRules as $resolvedRule) {
            if ($resolvedRule->getLayout() instanceof Layout) {
                return $resolvedRule;
            }
        }

        return null;
    }

    public function resolveRules(?Request $request = null, array $enabledConditions = []): array
    {
        $resolvedRules = $this->innerResolveRules($request, $enabledConditions);

        return array_values(
            array_filter(
                $resolvedRules,
                function (Rule $rule): bool {
                    return $rule->getLayout() instanceof Layout;
                }
            )
        );
    }

    public function matches(Rule $rule, Request $request, array $enabledConditions = []): bool
    {
        foreach ($rule->getConditions() as $condition) {
            $conditionType = $condition->getConditionType();

            if (!empty($enabledConditions) && !in_array($conditionType->getType(), $enabledConditions, true)) {
                continue;
            }

            if (!$conditionType->matches($request, $condition->getValue())) {
                return false;
            }
        }

        return true;
    }

    private function innerResolveRules(?Request $request = null, array $enabledConditions = []): array
    {
        if (!$request instanceof Request) {
            $request = $this->requestStack->getCurrentRequest();
        }

        if (!$request instanceof Request) {
            return [];
        }

        $resolvedRules = [];

        foreach ($this->targetTypeRegistry->getTargetTypes() as $targetType) {
            $targetValue = $targetType->provideValue($request);
            if ($targetValue === null) {
                continue;
            }

            $matchedRules = $this->layoutResolverService->matchRules($targetType->getType(), $targetValue);

            foreach ($matchedRules as $matchedRule) {
                if (!$matchedRule->isEnabled() || !$this->matches($matchedRule, $request, $enabledConditions)) {
                    continue;
                }

                $resolvedRules[] = $matchedRule;
            }
        }

        usort(
            $resolvedRules,
            function (Rule $a, Rule $b): int {
                return $b->getPriority() <=> $a->getPriority();
            }
        );

        return $resolvedRules;
    }
}
