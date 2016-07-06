<?php

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\API\Service\LayoutResolverService as APILayoutResolverService;
use Netgen\BlockManager\API\Values\TargetUpdateStruct;
use Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator;
use Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper;
use Netgen\BlockManager\API\Values\ConditionCreateStruct;
use Netgen\BlockManager\API\Values\ConditionUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\ConditionDraft;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetDraft;
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
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule If rule with speciled ID does not exist
     */
    public function loadRule($ruleId)
    {
        $this->validator->validateId($ruleId, 'ruleId');

        return $this->mapper->mapRule(
            $this->handler->loadRule(
                $ruleId,
                Rule::STATUS_PUBLISHED
            )
        );
    }

    /**
     * Loads a rule draft by its' ID.
     *
     * @param int|string $ruleId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If rule with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft
     */
    public function loadRuleDraft($ruleId)
    {
        $this->validator->validateId($ruleId, 'ruleId');

        return $this->mapper->mapRule(
            $this->handler->loadRule(
                $ruleId,
                Rule::STATUS_DRAFT
            )
        );
    }

    /**
     * Loads all rules.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule[]
     */
    public function loadRules()
    {
        $persistenceRules = $this->handler->loadRules(Rule::STATUS_PUBLISHED);

        $rules = array();
        foreach ($persistenceRules as $persistenceRule) {
            $rules[] = $this->mapper->mapRule($persistenceRule);
        }

        return $rules;
    }

    /**
     * Returns all rules that match specified target type and value.
     *
     * @param string $targetType
     * @param mixed $targetValue
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule[]
     */
    public function matchRules($targetType, $targetValue)
    {
        $persistenceRules = $this->handler->matchRules($targetType, $targetValue);

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
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If target with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Target
     */
    public function loadTarget($targetId)
    {
        $this->validator->validateId($targetId, 'targetId');

        return $this->mapper->mapTarget(
            $this->handler->loadTarget(
                $targetId,
                Rule::STATUS_PUBLISHED
            )
        );
    }

    /**
     * Loads a target draft by its' ID.
     *
     * @param int|string $targetId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If target with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\TargetDraft
     */
    public function loadTargetDraft($targetId)
    {
        $this->validator->validateId($targetId, 'targetId');

        return $this->mapper->mapTarget(
            $this->handler->loadTarget(
                $targetId,
                Rule::STATUS_DRAFT
            )
        );
    }

    /**
     * Loads a condition by its' ID.
     *
     * @param int|string $conditionId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If condition with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Condition
     */
    public function loadCondition($conditionId)
    {
        $this->validator->validateId($conditionId, 'conditionId');

        return $this->mapper->mapCondition(
            $this->handler->loadCondition(
                $conditionId,
                Rule::STATUS_PUBLISHED
            )
        );
    }

    /**
     * Loads a condition draft by its' ID.
     *
     * @param int|string $conditionId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If condition with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\ConditionDraft
     */
    public function loadConditionDraft($conditionId)
    {
        $this->validator->validateId($conditionId, 'conditionId');

        return $this->mapper->mapCondition(
            $this->handler->loadCondition(
                $conditionId,
                Rule::STATUS_DRAFT
            )
        );
    }

    /**
     * Creates a rule.
     *
     * @param \Netgen\BlockManager\API\Values\RuleCreateStruct $ruleCreateStruct
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft
     */
    public function createRule(RuleCreateStruct $ruleCreateStruct)
    {
        $this->validator->validateRuleCreateStruct($ruleCreateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $createdRule = $this->handler->createRule(
                $ruleCreateStruct,
                Rule::STATUS_DRAFT
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
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft $rule
     * @param \Netgen\BlockManager\API\Values\RuleUpdateStruct $ruleUpdateStruct
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft
     */
    public function updateRule(RuleDraft $rule, RuleUpdateStruct $ruleUpdateStruct)
    {
        $persistenceRule = $this->handler->loadRule($rule->getId(), Rule::STATUS_DRAFT);

        $this->validator->validateRuleUpdateStruct($ruleUpdateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedRule = $this->handler->updateRule(
                $persistenceRule,
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

        return $this->mapper->mapRule(
            $this->handler->loadRule($copiedRuleId, $persistenceRule->status)
        );
    }

    /**
     * Creates a rule draft.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If draft already exists for the rule
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft
     */
    public function createDraft(Rule $rule)
    {
        $persistenceRule = $this->handler->loadRule($rule->getId(), Rule::STATUS_PUBLISHED);

        if ($this->handler->ruleExists($persistenceRule->id, Rule::STATUS_DRAFT)) {
            throw new BadStateException('rule', 'The provided rule already has a draft.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $this->handler->deleteRule($persistenceRule->id, Rule::STATUS_DRAFT);
            $ruleDraft = $this->handler->createRuleStatus($persistenceRule, Rule::STATUS_DRAFT);
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapRule($ruleDraft);
    }

    /**
     * Discards a rule draft.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft $rule
     */
    public function discardDraft(RuleDraft $rule)
    {
        $persistenceRule = $this->handler->loadRule($rule->getId(), Rule::STATUS_DRAFT);

        $this->persistenceHandler->beginTransaction();

        try {
            $this->handler->deleteRule(
                $persistenceRule->id,
                Rule::STATUS_DRAFT
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();
    }

    /**
     * Publishes a rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft $rule
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function publishRule(RuleDraft $rule)
    {
        $persistenceRule = $this->handler->loadRule($rule->getId(), Rule::STATUS_DRAFT);

        $this->persistenceHandler->beginTransaction();

        try {
            $this->handler->deleteRule($persistenceRule->id, Rule::STATUS_ARCHIVED);

            if ($this->handler->ruleExists($persistenceRule->id, Rule::STATUS_PUBLISHED)) {
                $this->handler->createRuleStatus(
                    $this->handler->loadRule(
                        $persistenceRule->id,
                        Rule::STATUS_PUBLISHED
                    ),
                    Rule::STATUS_ARCHIVED
                );

                $this->handler->deleteRule($persistenceRule->id, Rule::STATUS_PUBLISHED);
            }

            $publishedRule = $this->handler->createRuleStatus($persistenceRule, Rule::STATUS_PUBLISHED);
            $this->handler->deleteRule($persistenceRule->id, Rule::STATUS_DRAFT);

            if ($publishedRule->layoutId === null || $this->handler->getTargetCount($publishedRule) === 0) {
                $this->handler->disableRule($publishedRule);
            }
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapRule(
            $this->handler->loadRule($rule->getId(), Rule::STATUS_PUBLISHED)
        );
    }

    /**
     * Deletes a rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     */
    public function deleteRule(Rule $rule)
    {
        $persistenceRule = $this->handler->loadRule($rule->getId(), $rule->getStatus());

        $this->persistenceHandler->beginTransaction();

        try {
            $this->handler->deleteRule(
                $persistenceRule->id
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
        $persistenceRule = $this->handler->loadRule($rule->getId(), Rule::STATUS_PUBLISHED);

        if ($persistenceRule->enabled) {
            throw new BadStateException('rule', 'Rule is already enabled.');
        }

        if ($persistenceRule->layoutId === null) {
            throw new BadStateException('rule', 'Rule is missing a layout and cannot be enabled.');
        }

        if ($this->handler->getTargetCount($persistenceRule) === 0) {
            throw new BadStateException('rule', 'Rule is missing targets and cannot be enabled.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $this->handler->enableRule($persistenceRule);
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
        $persistenceRule = $this->handler->loadRule($rule->getId(), Rule::STATUS_PUBLISHED);

        if (!$persistenceRule->enabled) {
            throw new BadStateException('rule', 'Rule is already disabled.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $this->handler->disableRule($persistenceRule);
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();
    }

    /**
     * Adds a target to rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft $rule
     * @param \Netgen\BlockManager\API\Values\TargetCreateStruct $targetCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If target of different type than it already exists in the rule is added
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\TargetDraft
     */
    public function addTarget(RuleDraft $rule, TargetCreateStruct $targetCreateStruct)
    {
        $persistenceRule = $this->handler->loadRule($rule->getId(), Rule::STATUS_DRAFT);
        $ruleTargets = $this->handler->loadRuleTargets($persistenceRule);

        if (!empty($ruleTargets) && $ruleTargets[0]->type !== $targetCreateStruct->type) {
            throw new BadStateException(
                'type',
                sprintf(
                    'Rule only accepts targets with "%s" target type',
                    $ruleTargets[0]->type
                )
            );
        }

        $this->validator->validateTargetCreateStruct($targetCreateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $createdTarget = $this->handler->addTarget(
                $persistenceRule,
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
     * Updates a target.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\TargetDraft $target
     * @param \Netgen\BlockManager\API\Values\TargetUpdateStruct $targetUpdateStruct
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\TargetDraft
     */
    public function updateTarget(TargetDraft $target, TargetUpdateStruct $targetUpdateStruct)
    {
        $persistenceTarget = $this->handler->loadTarget($target->getId(), Rule::STATUS_DRAFT);

        $this->validator->validateTargetUpdateStruct($target, $targetUpdateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedTarget = $this->handler->updateTarget(
                $persistenceTarget,
                $targetUpdateStruct
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapTarget($updatedTarget);
    }

    /**
     * Removes a target.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\TargetDraft $target
     */
    public function deleteTarget(TargetDraft $target)
    {
        $persistenceTarget = $this->handler->loadTarget($target->getId(), Rule::STATUS_DRAFT);

        $this->persistenceHandler->beginTransaction();

        try {
            $this->handler->deleteTarget($persistenceTarget);
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();
    }

    /**
     * Adds a condition to rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft $rule
     * @param \Netgen\BlockManager\API\Values\ConditionCreateStruct $conditionCreateStruct
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\ConditionDraft
     */
    public function addCondition(RuleDraft $rule, ConditionCreateStruct $conditionCreateStruct)
    {
        $persistenceRule = $this->handler->loadRule($rule->getId(), Rule::STATUS_DRAFT);

        $this->validator->validateConditionCreateStruct($conditionCreateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $createdCondition = $this->handler->addCondition(
                $persistenceRule,
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
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\ConditionDraft $condition
     * @param \Netgen\BlockManager\API\Values\ConditionUpdateStruct $conditionUpdateStruct
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\ConditionDraft
     */
    public function updateCondition(ConditionDraft $condition, ConditionUpdateStruct $conditionUpdateStruct)
    {
        $persistenceCondition = $this->handler->loadCondition($condition->getId(), Rule::STATUS_DRAFT);

        $this->validator->validateConditionUpdateStruct($condition, $conditionUpdateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedCondition = $this->handler->updateCondition(
                $persistenceCondition,
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
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\ConditionDraft $condition
     */
    public function deleteCondition(ConditionDraft $condition)
    {
        $persistenceCondition = $this->handler->loadCondition($condition->getId(), Rule::STATUS_DRAFT);

        $this->persistenceHandler->beginTransaction();

        try {
            $this->handler->deleteCondition($persistenceCondition);
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
     * @param string $type
     *
     * @return \Netgen\BlockManager\API\Values\TargetCreateStruct
     */
    public function newTargetCreateStruct($type)
    {
        return new TargetCreateStruct(
            array(
                'type' => $type,
            )
        );
    }

    /**
     * Creates a new target update struct.
     *
     * @return \Netgen\BlockManager\API\Values\TargetUpdateStruct
     */
    public function newTargetUpdateStruct()
    {
        return new TargetUpdateStruct();
    }

    /**
     * Creates a new condition create struct.
     *
     * @param string $type
     *
     * @return \Netgen\BlockManager\API\Values\ConditionCreateStruct
     */
    public function newConditionCreateStruct($type)
    {
        return new ConditionCreateStruct(
            array(
                'type' => $type,
            )
        );
    }

    /**
     * Creates a new condition update struct.
     *
     * @return \Netgen\BlockManager\API\Values\ConditionUpdateStruct
     */
    public function newConditionUpdateStruct()
    {
        return new ConditionUpdateStruct();
    }
}
