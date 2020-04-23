<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver;

use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use function array_filter;
use function array_values;
use function count;
use function in_array;
use function usort;

final class LayoutResolver implements LayoutResolverInterface
{
    /**
     * @var \Netgen\Layouts\API\Service\LayoutResolverService
     */
    private $layoutResolverService;

    /**
     * @var \Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry
     */
    private $targetTypeRegistry;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

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
                static function (Rule $rule): bool {
                    return $rule->getLayout() instanceof Layout;
                }
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

            $matchedRules = $this->layoutResolverService->matchRules($targetType::getType(), $targetValue);

            foreach ($matchedRules as $matchedRule) {
                if (!$matchedRule->isEnabled() || !$this->matches($matchedRule, $request, $enabledConditions)) {
                    continue;
                }

                $resolvedRules[] = $matchedRule;
            }
        }

        usort(
            $resolvedRules,
            static function (Rule $a, Rule $b): int {
                return $b->getPriority() <=> $a->getPriority();
            }
        );

        return $resolvedRules;
    }
}
