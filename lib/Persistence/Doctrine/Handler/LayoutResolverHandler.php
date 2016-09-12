<?php

namespace Netgen\BlockManager\Persistence\Doctrine\Handler;

use Netgen\BlockManager\API\Values\ConditionCreateStruct as APIConditionCreateStruct;
use Netgen\BlockManager\Persistence\Values\ConditionCreateStruct;
use Netgen\BlockManager\API\Values\ConditionUpdateStruct as APIConditionUpdateStruct;
use Netgen\BlockManager\Persistence\Values\ConditionUpdateStruct;
use Netgen\BlockManager\API\Values\RuleCreateStruct as APIRuleCreateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Target;
use Netgen\BlockManager\Persistence\Values\Page\Layout;
use Netgen\BlockManager\Persistence\Values\RuleCreateStruct;
use Netgen\BlockManager\API\Values\RuleUpdateStruct as APIRuleUpdateStruct;
use Netgen\BlockManager\API\Values\RuleMetadataUpdateStruct as APIRuleMetadataUpdateStruct;
use Netgen\BlockManager\Persistence\Values\RuleMetadataUpdateStruct;
use Netgen\BlockManager\Persistence\Values\RuleUpdateStruct;
use Netgen\BlockManager\API\Values\TargetCreateStruct as APITargetCreateStruct;
use Netgen\BlockManager\API\Values\TargetUpdateStruct as APITargetUpdateStruct;
use Netgen\BlockManager\Persistence\Values\TargetCreateStruct;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler;
use Netgen\BlockManager\Persistence\Handler\LayoutResolverHandler as LayoutResolverHandlerInterface;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Values\TargetUpdateStruct;

class LayoutResolverHandler implements LayoutResolverHandlerInterface
{
    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler
     */
    protected $queryHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper
     */
    protected $mapper;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler $queryHandler
     * @param \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper $mapper
     */
    public function __construct(LayoutResolverQueryHandler $queryHandler, LayoutResolverMapper $mapper)
    {
        $this->queryHandler = $queryHandler;
        $this->mapper = $mapper;
    }

    /**
     * Loads a rule with specified ID.
     *
     * @param int|string $ruleId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If rule with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule
     */
    public function loadRule($ruleId, $status)
    {
        $data = $this->queryHandler->loadRuleData($ruleId, $status);

        if (empty($data)) {
            throw new NotFoundException('rule', $ruleId);
        }

        $data = $this->mapper->mapRules($data);

        return reset($data);
    }

    /**
     * Loads all rules.
     *
     * @param int $status
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule[]
     */
    public function loadRules($status, $offset = 0, $limit = null)
    {
        $data = $this->queryHandler->loadRulesData($status, $offset, $limit);

        if (empty($data)) {
            return array();
        }

        $data = $this->mapper->mapRules($data);

        return $data;
    }

    /**
     * Returns the number of rules pointing to provided layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Layout $layout
     *
     * @return int
     */
    public function getRuleCount(Layout $layout)
    {
        return $this->queryHandler->getRuleCount($layout->id, Rule::STATUS_PUBLISHED);
    }

    /**
     * Returns all rules that match specified target type and value.
     *
     * @param string $targetType
     * @param mixed $targetValue
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule[]
     */
    public function matchRules($targetType, $targetValue)
    {
        $data = $this->queryHandler->matchRules($targetType, $targetValue);

        if (empty($data)) {
            return array();
        }

        $data = $this->mapper->mapRules($data);

        return $data;
    }

    /**
     * Loads an target with specified ID.
     *
     * @param int|string $targetId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If target with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Target
     */
    public function loadTarget($targetId, $status)
    {
        $data = $this->queryHandler->loadTargetData($targetId, $status);

        if (empty($data)) {
            throw new NotFoundException('target', $targetId);
        }

        $data = $this->mapper->mapTargets($data);

        return reset($data);
    }

    /**
     * Loads all targets that belong to rule with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Target[]
     */
    public function loadRuleTargets(Rule $rule)
    {
        return $this->mapper->mapTargets(
            $this->queryHandler->loadRuleTargetsData($rule->id, $rule->status)
        );
    }

    /**
     * Loads the count of targets within the rule with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     *
     * @return int
     */
    public function getTargetCount(Rule $rule)
    {
        return $this->queryHandler->getTargetCount($rule->id, $rule->status);
    }

    /**
     * Loads a condition with specified ID.
     *
     * @param int|string $conditionId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If condition with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition
     */
    public function loadCondition($conditionId, $status)
    {
        $data = $this->queryHandler->loadConditionData($conditionId, $status);

        if (empty($data)) {
            throw new NotFoundException('condition', $conditionId);
        }

        $data = $this->mapper->mapConditions($data);

        return reset($data);
    }

    /**
     * Loads all conditions that belong to rule with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition[]
     */
    public function loadRuleConditions(Rule $rule)
    {
        return $this->mapper->mapConditions(
            $this->queryHandler->loadRuleConditionsData($rule->id, $rule->status)
        );
    }

    /**
     * Returns if rule with specified ID exists.
     *
     * @param int|string $ruleId
     * @param int $status
     *
     * @return bool
     */
    public function ruleExists($ruleId, $status)
    {
        return $this->queryHandler->ruleExists($ruleId, $status);
    }

    /**
     * Creates a rule.
     *
     * @param \Netgen\BlockManager\API\Values\RuleCreateStruct $ruleCreateStruct
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule
     */
    public function createRule(APIRuleCreateStruct $ruleCreateStruct, $status)
    {
        $createdRuleId = $this->queryHandler->createRule(
            new RuleCreateStruct(
                array(
                    'layoutId' => $ruleCreateStruct->layoutId,
                    'priority' => $ruleCreateStruct->priority !== null ? $ruleCreateStruct->priority : 0,
                    'enabled' => $ruleCreateStruct->enabled !== null ? $ruleCreateStruct->enabled : false,
                    'comment' => $ruleCreateStruct->comment,
                    'status' => $status,
                )
            )
        );

        return $this->loadRule($createdRuleId, $status);
    }

    /**
     * Updates a rule with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     * @param \Netgen\BlockManager\API\Values\RuleUpdateStruct $ruleUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule
     */
    public function updateRule(Rule $rule, APIRuleUpdateStruct $ruleUpdateStruct)
    {
        $this->queryHandler->updateRule(
            $rule->id,
            $rule->status,
            new RuleUpdateStruct(
                array(
                    'layoutId' => $ruleUpdateStruct->layoutId !== null ? $ruleUpdateStruct->layoutId : $rule->layoutId,
                    'comment' => $ruleUpdateStruct->comment !== null ? $ruleUpdateStruct->comment : $rule->comment,
                )
            )
        );

        return $this->loadRule($rule->id, $rule->status);
    }

    /**
     * Updates rule metadata.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     * @param \Netgen\BlockManager\API\Values\RuleMetadataUpdateStruct $ruleUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule
     */
    public function updateRuleMetadata(Rule $rule, APIRuleMetadataUpdateStruct $ruleUpdateStruct)
    {
        $this->queryHandler->updateRuleData(
            $rule->id,
            new RuleMetadataUpdateStruct(
                array(
                    'enabled' => $rule->enabled,
                    'priority' => $ruleUpdateStruct->priority !== null ? $ruleUpdateStruct->priority : $rule->priority,
                )
            )
        );

        return $this->loadRule($rule->id, $rule->status);
    }

    /**
     * Copies a rule.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule
     */
    public function copyRule(Rule $rule)
    {
        // First copy the rule

        $copiedRuleId = $this->queryHandler->createRule(
            new RuleCreateStruct(
                array(
                    'layoutId' => $rule->layoutId,
                    'priority' => $rule->priority,
                    'enabled' => $rule->enabled,
                    'comment' => $rule->comment,
                    'status' => $rule->status,
                )
            )
        );

        // Then copy rule targets

        $ruleTargets = $this->loadRuleTargets($rule);

        foreach ($ruleTargets as $ruleTarget) {
            $this->queryHandler->addTarget(
                new TargetCreateStruct(
                    array(
                        'ruleId' => $copiedRuleId,
                        'status' => $ruleTarget->status,
                        'type' => $ruleTarget->type,
                        'value' => $ruleTarget->value,
                    )
                )
            );
        }

        // Then copy rule conditions

        $ruleConditions = $this->loadRuleConditions($rule);

        foreach ($ruleConditions as $ruleCondition) {
            $this->queryHandler->addCondition(
                new ConditionCreateStruct(
                    array(
                        'ruleId' => $copiedRuleId,
                        'status' => $ruleCondition->status,
                        'type' => $ruleCondition->type,
                        'value' => $ruleCondition->value,
                    )
                )
            );
        }

        return $this->loadRule($copiedRuleId, $rule->status);
    }

    /**
     * Creates a new rule status.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     * @param int $newStatus
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule
     */
    public function createRuleStatus(Rule $rule, $newStatus)
    {
        // First copy the rule

        $this->queryHandler->createRule(
            new RuleCreateStruct(
                array(
                    'layoutId' => $rule->layoutId,
                    'priority' => $rule->priority,
                    'enabled' => $rule->enabled,
                    'comment' => $rule->comment,
                    'status' => $newStatus,
                )
            ),
            $rule->id
        );

        // Then copy rule targets

        $ruleTargets = $this->loadRuleTargets($rule);

        foreach ($ruleTargets as $ruleTarget) {
            $this->queryHandler->addTarget(
                new TargetCreateStruct(
                    array(
                        'ruleId' => $ruleTarget->ruleId,
                        'status' => $newStatus,
                        'type' => $ruleTarget->type,
                        'value' => $ruleTarget->value,
                    )
                ),
                $ruleTarget->id
            );
        }

        // Then copy rule conditions

        $ruleConditions = $this->loadRuleConditions($rule);

        foreach ($ruleConditions as $ruleCondition) {
            $this->queryHandler->addCondition(
                new ConditionCreateStruct(
                    array(
                        'ruleId' => $ruleCondition->ruleId,
                        'status' => $newStatus,
                        'type' => $ruleCondition->type,
                        'value' => $ruleCondition->value,
                    )
                ),
                $ruleCondition->id
            );
        }

        return $this->loadRule($rule->id, $newStatus);
    }

    /**
     * Deletes a rule with specified ID.
     *
     * @param int|string $ruleId
     * @param int $status
     */
    public function deleteRule($ruleId, $status = null)
    {
        $this->queryHandler->deleteRuleTargets($ruleId, $status);
        $this->queryHandler->deleteRuleConditions($ruleId, $status);
        $this->queryHandler->deleteRule($ruleId, $status);
    }

    /**
     * Enables a rule.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule
     */
    public function enableRule(Rule $rule)
    {
        $this->queryHandler->updateRuleData(
            $rule->id,
            new RuleMetadataUpdateStruct(
                array(
                    'enabled' => true,
                    'priority' => $rule->priority,
                )
            )
        );

        return $this->loadRule($rule->id, $rule->status);
    }

    /**
     * Disables a rule.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule
     */
    public function disableRule(Rule $rule)
    {
        $this->queryHandler->updateRuleData(
            $rule->id,
            new RuleMetadataUpdateStruct(
                array(
                    'enabled' => false,
                    'priority' => $rule->priority,
                )
            )
        );

        return $this->loadRule($rule->id, $rule->status);
    }

    /**
     * Adds a target to rule.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     * @param \Netgen\BlockManager\API\Values\TargetCreateStruct $targetCreateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Target
     */
    public function addTarget(Rule $rule, APITargetCreateStruct $targetCreateStruct)
    {
        $createdTargetId = $this->queryHandler->addTarget(
            new TargetCreateStruct(
                array(
                    'ruleId' => $rule->id,
                    'status' => $rule->status,
                    'type' => $targetCreateStruct->type,
                    'value' => $targetCreateStruct->value,
                )
            )
        );

        return $this->loadTarget($createdTargetId, $rule->status);
    }

    /**
     * Updates a target with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Target $target
     * @param \Netgen\BlockManager\API\Values\TargetUpdateStruct $targetUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Target
     */
    public function updateTarget(Target $target, APITargetUpdateStruct $targetUpdateStruct)
    {
        $this->queryHandler->updateTarget(
            $target->id,
            $target->status,
            new TargetUpdateStruct(
                array(
                    'value' => $targetUpdateStruct->value,
                )
            )
        );

        return $this->loadTarget($target->id, $target->status);
    }

    /**
     * Removes a target.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Target $target
     */
    public function deleteTarget(Target $target)
    {
        $this->queryHandler->deleteTarget($target->id, $target->status);
    }

    /**
     * Adds a condition to rule.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     * @param \Netgen\BlockManager\API\Values\ConditionCreateStruct $conditionCreateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition
     */
    public function addCondition(Rule $rule, APIConditionCreateStruct $conditionCreateStruct)
    {
        $createdConditionId = $this->queryHandler->addCondition(
            new ConditionCreateStruct(
                array(
                    'ruleId' => $rule->id,
                    'status' => $rule->status,
                    'type' => $conditionCreateStruct->type,
                    'value' => $conditionCreateStruct->value,
                )
            )
        );

        return $this->loadCondition($createdConditionId, $rule->status);
    }

    /**
     * Updates a condition with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition $condition
     * @param \Netgen\BlockManager\API\Values\ConditionUpdateStruct $conditionUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition
     */
    public function updateCondition(Condition $condition, APIConditionUpdateStruct $conditionUpdateStruct)
    {
        $this->queryHandler->updateCondition(
            $condition->id,
            $condition->status,
            new ConditionUpdateStruct(
                array(
                    'value' => $conditionUpdateStruct->value,
                )
            )
        );

        return $this->loadCondition($condition->id, $condition->status);
    }

    /**
     * Removes a condition.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition $condition
     */
    public function deleteCondition(Condition $condition)
    {
        $this->queryHandler->deleteCondition($condition->id, $condition->status);
    }
}
