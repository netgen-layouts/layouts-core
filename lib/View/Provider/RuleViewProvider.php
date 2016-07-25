<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface;
use Netgen\BlockManager\View\View\RuleView;

class RuleViewProvider implements ViewProviderInterface
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
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface
     */
    protected $conditionTypeRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\LayoutResolverService $layoutResolverService
     * @param \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface $targetTypeRegistry
     * @param \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface $conditionTypeRegistry
     */
    public function __construct(
        LayoutResolverService $layoutResolverService,
        TargetTypeRegistryInterface $targetTypeRegistry,
        ConditionTypeRegistryInterface $conditionTypeRegistry
    ) {
        $this->layoutResolverService = $layoutResolverService;
        $this->targetTypeRegistry = $targetTypeRegistry;
        $this->conditionTypeRegistry = $conditionTypeRegistry;
    }

    /**
     * Provides the view.
     *
     * @param mixed $valueObject
     * @param array $parameters
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function provideView($valueObject, array $parameters = array())
    {
        /** @var \Netgen\BlockManager\API\Values\LayoutResolver\Rule $valueObject */
        $ruleView = new RuleView($valueObject);

        $ruleView->addParameters(
            array(
                'published_rule' => $this->loadPublishedRule($valueObject),
                'target_types' => $this->targetTypeRegistry->getTargetTypes(),
                'condition_types' => $this->conditionTypeRegistry->getConditionTypes(),
            )
        );

        return $ruleView;
    }

    /**
     * Returns if this view provider supports the given value object.
     *
     * @param mixed $valueObject
     *
     * @return bool
     */
    public function supports($valueObject)
    {
        return $valueObject instanceof Rule;
    }

    /**
     * Returns the published version of provided rule or null
     * if rule has no published version.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    protected function loadPublishedRule(Rule $rule)
    {
        if (!$rule instanceof RuleDraft) {
            return $rule;
        }

        try {
            return $this->layoutResolverService->loadRule(
                $rule->getId()
            );
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }
}
