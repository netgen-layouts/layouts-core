<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Core\Values\LayoutResolver\Target;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface;
use Netgen\BlockManager\Persistence\HandlerInterface;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition as PersistenceCondition;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule as PersistenceRule;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Target as PersistenceTarget;

final class LayoutResolverMapper
{
    /**
     * @var \Netgen\BlockManager\Persistence\HandlerInterface
     */
    private $persistenceHandler;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper
     */
    private $layoutMapper;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface
     */
    private $targetTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface
     */
    private $conditionTypeRegistry;

    public function __construct(
        HandlerInterface $persistenceHandler,
        LayoutMapper $layoutMapper,
        TargetTypeRegistryInterface $targetTypeRegistry,
        ConditionTypeRegistryInterface $conditionTypeRegistry
    ) {
        $this->persistenceHandler = $persistenceHandler;
        $this->layoutMapper = $layoutMapper;
        $this->targetTypeRegistry = $targetTypeRegistry;
        $this->conditionTypeRegistry = $conditionTypeRegistry;
    }

    /**
     * Builds the API rule value from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function mapRule(PersistenceRule $rule)
    {
        $handler = $this->persistenceHandler->getLayoutResolverHandler();

        $layout = null;
        try {
            // Layouts used by rule are always in published status
            $layout = $this->persistenceHandler->getLayoutHandler()->loadLayout(
                $rule->layoutId,
                Value::STATUS_PUBLISHED
            );

            $layout = $this->layoutMapper->mapLayout($layout);
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $persistenceTargets = $handler->loadRuleTargets($rule);

        $targets = array();
        foreach ($persistenceTargets as $persistenceTarget) {
            $targets[] = $this->mapTarget($persistenceTarget);
        }

        $persistenceConditions = $handler->loadRuleConditions($rule);

        $conditions = array();
        foreach ($persistenceConditions as $persistenceCondition) {
            $conditions[] = $this->mapCondition($persistenceCondition);
        }

        $ruleData = array(
            'id' => $rule->id,
            'status' => $rule->status,
            'layout' => $layout,
            'enabled' => $rule->enabled,
            'priority' => $rule->priority,
            'comment' => $rule->comment,
            'targets' => $targets,
            'conditions' => $conditions,
            'published' => $rule->status === Value::STATUS_PUBLISHED,
        );

        return new Rule($ruleData);
    }

    /**
     * Builds the API target value from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Target $target
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Target
     */
    public function mapTarget(PersistenceTarget $target)
    {
        $targetData = array(
            'id' => $target->id,
            'status' => $target->status,
            'ruleId' => $target->ruleId,
            'targetType' => $this->targetTypeRegistry->getTargetType(
                $target->type
            ),
            'published' => $target->status === Value::STATUS_PUBLISHED,
            'value' => $target->value,
        );

        return new Target($targetData);
    }

    /**
     * Builds the API condition value from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition $condition
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Condition
     */
    public function mapCondition(PersistenceCondition $condition)
    {
        $conditionData = array(
            'id' => $condition->id,
            'status' => $condition->status,
            'ruleId' => $condition->ruleId,
            'conditionType' => $this->conditionTypeRegistry->getConditionType(
                $condition->type
            ),
            'published' => $condition->status === Value::STATUS_PUBLISHED,
            'value' => $condition->value,
        );

        return new Condition($conditionData);
    }
}
