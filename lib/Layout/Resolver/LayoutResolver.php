<?php

namespace Netgen\BlockManager\Layout\Resolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class LayoutResolver implements LayoutResolverInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    protected $layoutResolverService;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface
     */
    protected $targetTypeRegistry;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\LayoutResolverService $layoutResolverService
     * @param \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface $targetTypeRegistry
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     */
    public function __construct(
        LayoutResolverService $layoutResolverService,
        TargetTypeRegistryInterface $targetTypeRegistry,
        RequestStack $requestStack
    ) {
        $this->layoutResolverService = $layoutResolverService;
        $this->targetTypeRegistry = $targetTypeRegistry;
        $this->requestStack = $requestStack;
    }

    /**
     * Resolves the rules based on the provided request.
     *
     * If no request is provided, current request is used.
     *
     * Rules are sorted based on their priorities, descending,
     * meaning the rule with highest priority will be the first one in the list.
     *
     * Rules with same priorities will have undetermined relative positions between each other.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param bool $matchConditions
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule[]
     */
    public function resolveRules(Request $request = null, $matchConditions = true)
    {
        $request = $request ?: $this->requestStack->getCurrentRequest();

        $resolvedRules = array();

        foreach ($this->targetTypeRegistry->getTargetTypes() as $targetType) {
            $targetValue = $targetType->provideValue($request);
            if ($targetValue === null) {
                continue;
            }

            $matchedRules = $this->matchRules($targetType->getType(), $targetValue);
            foreach ($matchedRules as $matchedRule) {
                if ($matchConditions && !$this->matches($matchedRule, $request)) {
                    continue;
                }

                $resolvedRules[] = $matchedRule;
            }
        }

        usort(
            $resolvedRules,
            function (Rule $a, Rule $b) {
                return $b->getPriority() - $a->getPriority();
            }
        );

        return $resolvedRules;
    }

    /**
     * Matches the rules based on provided target type and value.
     *
     * @param string $targetType
     * @param mixed $targetValue
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule[]
     */
    public function matchRules($targetType, $targetValue)
    {
        $rules = $this->layoutResolverService->matchRules($targetType, $targetValue);

        if (empty($rules)) {
            return array();
        }

        $matchedRules = array();

        foreach ($rules as $rule) {
            if (!$rule->isEnabled() || !$rule->getLayout() instanceof Layout) {
                continue;
            }

            $matchedRules[] = $rule;
        }

        return $matchedRules;
    }

    /**
     * Returns true if the rule matches the provided request.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return bool If condition type does not exist for one of the conditions
     */
    public function matches(Rule $rule, Request $request)
    {
        foreach ($rule->getConditions() as $condition) {
            $conditionType = $condition->getConditionType();
            if (!$conditionType->matches($request, $condition->getValue())) {
                return false;
            }
        }

        return true;
    }
}
