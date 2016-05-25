<?php

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\API\Service\LayoutResolverService as APILayoutResolverService;
use Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator;
use Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper;
use Netgen\BlockManager\API\Values\ConditionCreateStruct;
use Netgen\BlockManager\API\Values\ConditionUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\API\Values\LayoutResolver\Target;
use Netgen\BlockManager\API\Values\RuleCreateStruct;
use Netgen\BlockManager\API\Values\RuleUpdateStruct;
use Netgen\BlockManager\API\Values\TargetCreateStruct;
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\Exception\BadStateException;
use Exception;

class LayoutResolverService implements APILayoutResolverService
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator
     */
    protected $validator;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper
     */
    protected $mapper;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler
     */
    protected $persistenceHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\LayoutResolverHandler
     */
    protected $handler;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator $validator
     * @param \Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper $mapper
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     */
    public function __construct(
        LayoutResolverValidator $validator,
        LayoutResolverMapper $mapper,
        Handler $persistenceHandler
    ) {
        $this->validator = $validator;
        $this->mapper = $mapper;
        $this->persistenceHandler = $persistenceHandler;

        $this->handler = $persistenceHandler->getLayoutResolverHandler();
    }

    /**
     * Loads a rule by its' ID.
     *
     * @param int|string $ruleId
     * @param int $status
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule If rule with specified ID does not exist
     */
    public function loadRule($ruleId, $status = Rule::STATUS_PUBLISHED)
    {
        $this->validator->validateId($ruleId, 'ruleId');

        return $this->mapper->mapRule(
            $this->handler->loadRule(
                $ruleId,
                $status
            )
        );
    }

    /**
     * Loads all rules.
     *
     * @param int $status
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule[]
     */
    public function loadRules($status = Rule::STATUS_PUBLISHED)
    {
        $persistenceRules = $this->handler->loadRules($status);

        $rules = array();
        foreach ($persistenceRules as $persistenceRule) {
            $rules[] = $this->mapper->mapRule($persistenceRule);
        }

        return $rules;
    }

    /**
     * Loads a target by its' ID.
     *
     * @param int|string $targetId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If target with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Target
     */
    public function loadTarget($targetId, $status = Rule::STATUS_PUBLISHED)
    {
        $this->validator->validateId($targetId, 'targetId');

        return $this->mapper->mapTarget(
            $this->handler->loadTarget(
                $targetId,
                $status
            )
        );
    }

    /**
     * Loads a condition by its' ID.
     *
     * @param int|string $conditionId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If condition with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Condition
     */
    public function loadCondition($conditionId, $status = Rule::STATUS_PUBLISHED)
    {
        $this->validator->validateId($conditionId, 'conditionId');

        return $this->mapper->mapCondition(
            $this->handler->loadCondition(
                $conditionId,
                $status
            )
        );
    }

    /**
     * Creates a rule.
     *
     * @param \Netgen\BlockManager\API\Values\RuleCreateStruct $ruleCreateStruct
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function createRule(RuleCreateStruct $ruleCreateStruct)
    {
        $this->validator->validateRuleCreateStruct($ruleCreateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $createdRule = $this->handler->createRule(
                $ruleCreateStruct
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapRule($createdRule);
    }

    /**
     * Updates a rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     * @param \Netgen\BlockManager\API\Values\RuleUpdateStruct $ruleUpdateStruct
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function updateRule(Rule $rule, RuleUpdateStruct $ruleUpdateStruct)
    {
        $persistenceRule = $this->handler->loadRule($rule->getId(), $rule->getStatus());

        $this->validator->validateRuleUpdateStruct($ruleUpdateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedRule = $this->handler->updateRule(
                $persistenceRule->id,
                $persistenceRule->status,
                $ruleUpdateStruct
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapRule($updatedRule);
    }

    /**
     * Copies a rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function copyRule(Rule $rule)
    {
        $persistenceRule = $this->handler->loadRule($rule->getId(), $rule->getStatus());

        $this->persistenceHandler->beginTransaction();

        try {
            $copiedRuleId = $this->handler->copyRule(
                $persistenceRule->id
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->loadRule($copiedRuleId, $persistenceRule->status);
    }

    /**
     * Creates a new rule status.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     * @param int $status
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule already has the provided status
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function createRuleStatus(Rule $rule, $status)
    {
        $persistenceRule = $this->handler->loadRule($rule->getId(), $rule->getStatus());

        if ($this->handler->ruleExists($persistenceRule->id, $status)) {
            throw new BadStateException('status', 'Rule already has the provided status.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $createdRule = $this->handler->createRuleStatus(
                $persistenceRule->id,
                $persistenceRule->status,
                $status
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapRule($createdRule);
    }

    /**
     * Creates a rule draft.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not published
     *                                                          If draft already exists for the rule
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function createDraft(Rule $rule)
    {
        $persistenceRule = $this->handler->loadRule($rule->getId(), $rule->getStatus());

        if ($persistenceRule->status !== Rule::STATUS_PUBLISHED) {
            throw new BadStateException('rule', 'Drafts can be created only from published rules.');
        }

        if ($this->handler->ruleExists($persistenceRule->id, Rule::STATUS_DRAFT)) {
            throw new BadStateException('rule', 'The provided rule already has a draft.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $this->handler->deleteRule($persistenceRule->id, Rule::STATUS_DRAFT);
            $ruleDraft = $this->handler->createRuleStatus($persistenceRule->id, Rule::STATUS_PUBLISHED, Rule::STATUS_DRAFT);
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapRule($ruleDraft);
    }

    /**
     * Publishes a rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function publishRule(Rule $rule)
    {
        $persistenceRule = $this->handler->loadRule($rule->getId(), $rule->getStatus());

        if ($persistenceRule->status !== Rule::STATUS_DRAFT) {
            throw new BadStateException('rule', 'Only rules in draft status can be published.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $this->handler->deleteRule($persistenceRule->id, Rule::STATUS_ARCHIVED);

            $this->handler->createRuleStatus($persistenceRule->id, Rule::STATUS_PUBLISHED, Rule::STATUS_ARCHIVED);
            $this->handler->deleteRule($persistenceRule->id, Rule::STATUS_PUBLISHED);

            $publishedRule = $this->handler->createRuleStatus($persistenceRule->id, Rule::STATUS_DRAFT, Rule::STATUS_PUBLISHED);
            $this->handler->deleteRule($persistenceRule->id, Rule::STATUS_DRAFT);
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapRule($publishedRule);
    }

    /**
     * Deletes a rule.
     *
     * @param bool $deleteAllStatuses
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     */
    public function deleteRule(Rule $rule, $deleteAllStatuses = false)
    {
        $persistenceRule = $this->handler->loadRule($rule->getId(), $rule->getStatus());

        $this->persistenceHandler->beginTransaction();

        try {
            $this->handler->deleteRule(
                $persistenceRule->id,
                $deleteAllStatuses ? null : $persistenceRule->status
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();
    }

    /**
     * Enables a rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule cannot be enabled
     */
    public function enableRule(Rule $rule)
    {
        $persistenceRule = $this->handler->loadRule($rule->getId(), $rule->getStatus());

        if ($persistenceRule->status !== Rule::STATUS_PUBLISHED) {
            throw new BadStateException('rule', 'Rule is not published and cannot be enabled.');
        }

        if ($persistenceRule->enabled) {
            throw new BadStateException('rule', 'Rule is already enabled.');
        }

        if ($persistenceRule->layoutId === null) {
            throw new BadStateException('rule', 'Rule is missing a layout and cannot be enabled.');
        }

        if ($this->handler->getTargetCount($persistenceRule->id, $persistenceRule->status) === 0) {
            throw new BadStateException('rule', 'Rule is missing targets and cannot be enabled.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $this->handler->enableRule($persistenceRule->id);
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();
    }

    /**
     * Disables a rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule cannot be disabled
     */
    public function disableRule(Rule $rule)
    {
        $persistenceRule = $this->handler->loadRule($rule->getId(), $rule->getStatus());

        if ($persistenceRule->status !== Rule::STATUS_PUBLISHED) {
            throw new BadStateException('rule', 'Rule is not published and cannot be disabled.');
        }

        if (!$persistenceRule->enabled) {
            throw new BadStateException('rule', 'Rule is already disabled.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $this->handler->disableRule($persistenceRule->id);
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();
    }

    /**
     * Adds a target to rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     * @param \Netgen\BlockManager\API\Values\TargetCreateStruct $targetCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If target of different type than it already exists in the rule is added
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Target
     */
    public function addTarget(Rule $rule, TargetCreateStruct $targetCreateStruct)
    {
        $persistenceRule = $this->handler->loadRule($rule->getId(), $rule->getStatus());

        $this->validator->validateTargetCreateStruct($targetCreateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $createdTarget = $this->handler->addTarget(
                $persistenceRule->id,
                $persistenceRule->status,
                $targetCreateStruct
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapTarget($createdTarget);
    }

    /**
     * Removes a target.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Target $target
     */
    public function removeTarget(Target $target)
    {
        $persistenceTarget = $this->handler->loadTarget($target->getId(), $target->getStatus());

        $this->persistenceHandler->beginTransaction();

        try {
            $this->handler->deleteTarget(
                $persistenceTarget->id,
                $persistenceTarget->status
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();
    }

    /**
     * Adds a condition to rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     * @param \Netgen\BlockManager\API\Values\ConditionCreateStruct $conditionCreateStruct
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Condition
     */
    public function addCondition(Rule $rule, ConditionCreateStruct $conditionCreateStruct)
    {
        $persistenceRule = $this->handler->loadRule($rule->getId(), $rule->getStatus());

        $this->validator->validateConditionCreateStruct($conditionCreateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $createdCondition = $this->handler->addCondition(
                $persistenceRule->id,
                $persistenceRule->status,
                $conditionCreateStruct
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapCondition($createdCondition);
    }

    /**
     * Updates a condition.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Condition $condition
     * @param \Netgen\BlockManager\API\Values\ConditionUpdateStruct $conditionUpdateStruct
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Condition
     */
    public function updateCondition(Condition $condition, ConditionUpdateStruct $conditionUpdateStruct)
    {
        $persistenceCondition = $this->handler->loadCondition($condition->getId(), $condition->getStatus());

        $this->validator->validateConditionUpdateStruct($conditionUpdateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedCondition = $this->handler->updateCondition(
                $persistenceCondition->id,
                $persistenceCondition->status,
                $conditionUpdateStruct
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapCondition($updatedCondition);
    }

    /**
     * Removes a condition.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Condition $condition
     */
    public function removeCondition(Condition $condition)
    {
        $persistenceCondition = $this->handler->loadCondition($condition->getId(), $condition->getStatus());

        $this->persistenceHandler->beginTransaction();

        try {
            $this->handler->deleteCondition(
                $persistenceCondition->id,
                $persistenceCondition->status
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();
    }

    /**
     * Creates a new rule create struct.
     *
     * @return \Netgen\BlockManager\API\Values\RuleCreateStruct
     */
    public function newRuleCreateStruct()
    {
        return new RuleCreateStruct();
    }

    /**
     * Creates a new rule update struct.
     *
     * @return \Netgen\BlockManager\API\Values\RuleUpdateStruct
     */
    public function newRuleUpdateStruct()
    {
        return new RuleUpdateStruct();
    }

    /**
     * Creates a new target create struct.
     *
     * @param string $identifier
     * @param mixed $value
     *
     * @return \Netgen\BlockManager\API\Values\TargetCreateStruct
     */
    public function newTargetCreateStruct($identifier, $value)
    {
        return new TargetCreateStruct(
            array(
                'identifier' => $identifier,
                'value' => $value,
            )
        );
    }

    /**
     * Creates a new condition create struct.
     *
     * @param string $identifier
     * @param mixed $value
     *
     * @return \Netgen\BlockManager\API\Values\ConditionCreateStruct
     */
    public function newConditionCreateStruct($identifier, $value)
    {
        return new ConditionCreateStruct(
            array(
                'identifier' => $identifier,
                'value' => $value,
            )
        );
    }

    /**
     * Creates a new condition update struct.
     *
     * @param mixed $value
     *
     * @return \Netgen\BlockManager\API\Values\ConditionUpdateStruct
     */
    public function newConditionUpdateStruct($value)
    {
        return new ConditionUpdateStruct(
            array(
                'value' => $value,
            )
        );
    }
}
