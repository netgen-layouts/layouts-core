<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule as PersistenceRule;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Target as PersistenceTarget;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition as PersistenceCondition;
use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Core\Values\LayoutResolver\RuleDraft;
use Netgen\BlockManager\Core\Values\LayoutResolver\Target;
use Netgen\BlockManager\Core\Values\LayoutResolver\TargetDraft;
use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Core\Values\LayoutResolver\ConditionDraft;

class LayoutResolverMapper extends Mapper
{
    /**
     * Builds the API rule value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function mapRule(PersistenceRule $rule)
    {
        $handler = $this->persistenceHandler->getLayoutResolverHandler();

        $persistenceTargets = $handler->loadRuleTargets(
            $rule->id,
            $rule->status
        );

        $targets = array();
        foreach ($persistenceTargets as $persistenceTarget) {
            $targets[] = $this->mapTarget($persistenceTarget);
        }

        $persistenceConditions = $handler->loadRuleConditions(
            $rule->id,
            $rule->status
        );

        $conditions = array();
        foreach ($persistenceConditions as $persistenceCondition) {
            $conditions[] = $this->mapCondition($persistenceCondition);
        }

        $ruleData = array(
            'id' => $rule->id,
            'status' => $rule->status,
            'layoutId' => $rule->layoutId,
            'enabled' => $rule->enabled,
            'priority' => $rule->priority,
            'comment' => $rule->comment,
            'targets' => $targets,
            'conditions' => $conditions,
        );

        return $rule->status === PersistenceRule::STATUS_PUBLISHED ?
            new Rule($ruleData) :
            new RuleDraft($ruleData);
    }

    /**
     * Builds the API target value object from persistence one.
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
            'identifier' => $target->identifier,
            'value' => $target->value,
        );

        return $target->status === PersistenceRule::STATUS_PUBLISHED ?
            new Target($targetData) :
            new TargetDraft($targetData);
    }

    /**
     * Builds the API condition value object from persistence one.
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
            'identifier' => $condition->identifier,
            'value' => $condition->value,
        );

        return $condition->status === PersistenceRule::STATUS_PUBLISHED ?
            new Condition($conditionData) :
            new ConditionDraft($conditionData);
    }
}
