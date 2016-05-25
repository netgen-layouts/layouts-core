<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule as PersistenceRule;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Target as PersistenceTarget;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition as PersistenceCondition;
use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Core\Values\LayoutResolver\Target;
use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;

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

        return new Rule(
            array(
                'id' => $rule->id,
                'status' => $rule->status,
                'layoutId' => $rule->layoutId,
                'enabled' => $rule->enabled,
                'priority' => $rule->priority,
                'comment' => $rule->comment,
                'targets' => $targets,
                'conditions' => $conditions,
            )
        );
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
        return new Target(
            array(
                'id' => $target->id,
                'status' => $target->status,
                'ruleId' => $target->ruleId,
                'identifier' => $target->identifier,
                'value' => $target->value,
            )
        );
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
        return new Condition(
            array(
                'id' => $condition->id,
                'status' => $condition->status,
                'ruleId' => $condition->ruleId,
                'identifier' => $condition->identifier,
                'value' => $condition->value,
            )
        );
    }
}
