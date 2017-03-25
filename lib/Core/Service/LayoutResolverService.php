<?php

namespace Netgen\BlockManager\Core\Service;

use Exception;
use Netgen\BlockManager\API\Service\LayoutResolverService as APILayoutResolverService;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\BlockManager\API\Values\LayoutResolver\ConditionCreateStruct as APIConditionCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\ConditionUpdateStruct as APIConditionUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleCreateStruct as APIRuleCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleMetadataUpdateStruct as APIRuleMetadataUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleUpdateStruct as APIRuleUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\Target;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetCreateStruct as APITargetCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetUpdateStruct as APITargetUpdateStruct;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper;
use Netgen\BlockManager\Core\Service\StructBuilder\LayoutResolverStructBuilder;
use Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\RuleCreateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\TargetCreateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\TargetUpdateStruct;

class LayoutResolverService extends Service implements APILayoutResolverService
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
     * @var \Netgen\BlockManager\Core\Service\StructBuilder\LayoutResolverStructBuilder
     */
    protected $structBuilder;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\LayoutResolverHandler
     */
    protected $handler;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     * @param \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator $validator
     * @param \Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper $mapper
     * @param \Netgen\BlockManager\Core\Service\StructBuilder\LayoutResolverStructBuilder $structBuilder
     */
    public function __construct(
        Handler $persistenceHandler,
        LayoutResolverValidator $validator,
        LayoutResolverMapper $mapper,
        LayoutResolverStructBuilder $structBuilder
    ) {
        parent::__construct($persistenceHandler);

        $this->validator = $validator;
        $this->mapper = $mapper;
        $this->structBuilder = $structBuilder;

        $this->handler = $persistenceHandler->getLayoutResolverHandler();
    }

    /**
     * Loads a rule by its' ID.
     *
     * @param int|string $ruleId
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule If rule with specified ID does not exist
     */
    public function loadRule($ruleId)
    {
        $this->validator->validateId($ruleId, 'ruleId');

        return $this->mapper->mapRule(
            $this->handler->loadRule(
                $ruleId,
                Value::STATUS_PUBLISHED
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
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function loadRuleDraft($ruleId)
    {
        $this->validator->validateId($ruleId, 'ruleId');

        return $this->mapper->mapRule(
            $this->handler->loadRule(
                $ruleId,
                Value::STATUS_DRAFT
            )
        );
    }

    /**
     * Loads all rules.
     *
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule[]
     */
    public function loadRules($offset = 0, $limit = null)
    {
        $this->validator->validateOffsetAndLimit($offset, $limit);

        $persistenceRules = $this->handler->loadRules(
            Value::STATUS_PUBLISHED,
            $offset,
            $limit
        );

        $rules = array();
        foreach ($persistenceRules as $persistenceRule) {
            $rules[] = $this->mapper->mapRule($persistenceRule);
        }

        return $rules;
    }

    /**
     * Returns the number of rules pointing to provided layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     *
     * @return int
     */
    public function getRuleCount(Layout $layout)
    {
        if (!$layout->isPublished()) {
            throw new BadStateException('layout', 'Only published layouts can be used in rules.');
        }

        $persistenceLayout = $this->persistenceHandler->getLayoutHandler()->loadLayout(
            $layout->getId(),
            Value::STATUS_PUBLISHED
        );

        return $this->handler->getRuleCount($persistenceLayout);
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
                Value::STATUS_PUBLISHED
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
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Target
     */
    public function loadTargetDraft($targetId)
    {
        $this->validator->validateId($targetId, 'targetId');

        return $this->mapper->mapTarget(
            $this->handler->loadTarget(
                $targetId,
                Value::STATUS_DRAFT
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
                Value::STATUS_PUBLISHED
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
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Condition
     */
    public function loadConditionDraft($conditionId)
    {
        $this->validator->validateId($conditionId, 'conditionId');

        return $this->mapper->mapCondition(
            $this->handler->loadCondition(
                $conditionId,
                Value::STATUS_DRAFT
            )
        );
    }

    /**
     * Creates a rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\RuleCreateStruct $ruleCreateStruct
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function createRule(APIRuleCreateStruct $ruleCreateStruct)
    {
        $this->validator->validateRuleCreateStruct($ruleCreateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $createdRule = $this->handler->createRule(
                new RuleCreateStruct(
                    array(
                        'layoutId' => $ruleCreateStruct->layoutId,
                        'priority' => $ruleCreateStruct->priority,
                        'enabled' => $ruleCreateStruct->enabled,
                        'comment' => $ruleCreateStruct->comment,
                        'status' => Value::STATUS_DRAFT,
                    )
                )
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
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\RuleUpdateStruct $ruleUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function updateRule(Rule $rule, APIRuleUpdateStruct $ruleUpdateStruct)
    {
        if ($rule->isPublished()) {
            throw new BadStateException('rule', 'Only draft rules can be updated.');
        }

        $persistenceRule = $this->handler->loadRule($rule->getId(), Value::STATUS_DRAFT);

        $this->validator->validateRuleUpdateStruct($ruleUpdateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedRule = $this->handler->updateRule(
                $persistenceRule,
                new RuleUpdateStruct(
                    array(
                        'layoutId' => $ruleUpdateStruct->layoutId,
                        'comment' => $ruleUpdateStruct->comment,
                    )
                )
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapRule($updatedRule);
    }

    /**
     * Updates rule metadata.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\RuleMetadataUpdateStruct $ruleUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not published
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function updateRuleMetadata(Rule $rule, APIRuleMetadataUpdateStruct $ruleUpdateStruct)
    {
        if (!$rule->isPublished()) {
            throw new BadStateException('rule', 'Metadata can be updated only for published rules.');
        }

        $persistenceRule = $this->handler->loadRule($rule->getId(), Value::STATUS_PUBLISHED);

        $this->validator->validateRuleMetadataUpdateStruct($ruleUpdateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedRule = $this->handler->updateRuleMetadata(
                $persistenceRule,
                new RuleMetadataUpdateStruct(
                    array(
                        'priority' => $ruleUpdateStruct->priority,
                    )
                )
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
            $copiedRule = $this->handler->copyRule($persistenceRule);
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapRule($copiedRule);
    }

    /**
     * Creates a rule draft.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     * @param bool $discardExisting
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not published
     *                                                          If draft already exists for the rule and $discardExisting is set to false
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function createDraft(Rule $rule, $discardExisting = false)
    {
        if (!$rule->isPublished()) {
            throw new BadStateException('rule', 'Drafts can only be created from published rules.');
        }

        $persistenceRule = $this->handler->loadRule($rule->getId(), Value::STATUS_PUBLISHED);

        if ($this->handler->ruleExists($persistenceRule->id, Value::STATUS_DRAFT)) {
            if (!$discardExisting) {
                throw new BadStateException('rule', 'The provided rule already has a draft.');
            }
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $this->handler->deleteRule($persistenceRule->id, Value::STATUS_DRAFT);
            $ruleDraft = $this->handler->createRuleStatus($persistenceRule, Value::STATUS_DRAFT);
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
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not a draft
     */
    public function discardDraft(Rule $rule)
    {
        if ($rule->isPublished()) {
            throw new BadStateException('rule', 'Only draft rules can be discarded.');
        }

        $persistenceRule = $this->handler->loadRule($rule->getId(), Value::STATUS_DRAFT);

        $this->persistenceHandler->beginTransaction();

        try {
            $this->handler->deleteRule(
                $persistenceRule->id,
                Value::STATUS_DRAFT
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
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function publishRule(Rule $rule)
    {
        if ($rule->isPublished()) {
            throw new BadStateException('rule', 'Only draft rules can be published.');
        }

        $persistenceRule = $this->handler->loadRule($rule->getId(), Value::STATUS_DRAFT);

        $this->persistenceHandler->beginTransaction();

        try {
            $this->handler->deleteRule($persistenceRule->id, Value::STATUS_ARCHIVED);

            if ($this->handler->ruleExists($persistenceRule->id, Value::STATUS_PUBLISHED)) {
                $this->handler->createRuleStatus(
                    $this->handler->loadRule(
                        $persistenceRule->id,
                        Value::STATUS_PUBLISHED
                    ),
                    Value::STATUS_ARCHIVED
                );

                $this->handler->deleteRule($persistenceRule->id, Value::STATUS_PUBLISHED);
            }

            $publishedRule = $this->handler->createRuleStatus($persistenceRule, Value::STATUS_PUBLISHED);
            $this->handler->deleteRule($persistenceRule->id, Value::STATUS_DRAFT);

            if ($publishedRule->layoutId === null || $this->handler->getTargetCount($publishedRule) === 0) {
                $publishedRule = $this->handler->updateRuleMetadata(
                    $publishedRule,
                    new RuleMetadataUpdateStruct(
                        array(
                            'enabled' => false,
                        )
                    )
                );
            }
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
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not published
     *                                                          If rule cannot be enabled
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function enableRule(Rule $rule)
    {
        if (!$rule->isPublished()) {
            throw new BadStateException('rule', 'Only published rules can be enabled.');
        }

        $persistenceRule = $this->handler->loadRule($rule->getId(), Value::STATUS_PUBLISHED);

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
            $updatedRule = $this->handler->updateRuleMetadata(
                $persistenceRule,
                new RuleMetadataUpdateStruct(
                    array(
                        'enabled' => true,
                    )
                )
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapRule($updatedRule);
    }

    /**
     * Disables a rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not published
     *                                                          If rule cannot be disabled
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function disableRule(Rule $rule)
    {
        if (!$rule->isPublished()) {
            throw new BadStateException('rule', 'Only published rules can be disabled.');
        }

        $persistenceRule = $this->handler->loadRule($rule->getId(), Value::STATUS_PUBLISHED);

        if (!$persistenceRule->enabled) {
            throw new BadStateException('rule', 'Rule is already disabled.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedRule = $this->handler->updateRuleMetadata(
                $persistenceRule,
                new RuleMetadataUpdateStruct(
                    array(
                        'enabled' => false,
                    )
                )
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapRule($updatedRule);
    }

    /**
     * Adds a target to rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\TargetCreateStruct $targetCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not a draft
     *                                                          If target of different type than it already exists in the rule is added
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Target
     */
    public function addTarget(Rule $rule, APITargetCreateStruct $targetCreateStruct)
    {
        if ($rule->isPublished()) {
            throw new BadStateException('rule', 'Targets can be added only to draft rules.');
        }

        $persistenceRule = $this->handler->loadRule($rule->getId(), Value::STATUS_DRAFT);
        $ruleTargets = $this->handler->loadRuleTargets($persistenceRule);

        if (!empty($ruleTargets) && $ruleTargets[0]->type !== $targetCreateStruct->type) {
            throw new BadStateException(
                'rule',
                sprintf(
                    'Rule with ID "%s" only accepts targets with "%s" target type.',
                    $rule->getId(),
                    $ruleTargets[0]->type
                )
            );
        }

        $this->validator->validateTargetCreateStruct($targetCreateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $createdTarget = $this->handler->addTarget(
                $persistenceRule,
                new TargetCreateStruct(
                    array(
                        'type' => $targetCreateStruct->type,
                        'value' => $targetCreateStruct->value,
                    )
                )
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
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Target $target
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\TargetUpdateStruct $targetUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Target
     */
    public function updateTarget(Target $target, APITargetUpdateStruct $targetUpdateStruct)
    {
        if ($target->isPublished()) {
            throw new BadStateException('target', 'Only draft targets can be updated.');
        }

        $persistenceTarget = $this->handler->loadTarget($target->getId(), Value::STATUS_DRAFT);

        $this->validator->validateTargetUpdateStruct($target, $targetUpdateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedTarget = $this->handler->updateTarget(
                $persistenceTarget,
                new TargetUpdateStruct(
                    array(
                        'value' => $targetUpdateStruct->value,
                    )
                )
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
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Target $target
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not a draft
     */
    public function deleteTarget(Target $target)
    {
        if ($target->isPublished()) {
            throw new BadStateException('target', 'Only draft targets can be deleted.');
        }

        $persistenceTarget = $this->handler->loadTarget($target->getId(), Value::STATUS_DRAFT);

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
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\ConditionCreateStruct $conditionCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Condition
     */
    public function addCondition(Rule $rule, APIConditionCreateStruct $conditionCreateStruct)
    {
        if ($rule->isPublished()) {
            throw new BadStateException('rule', 'Conditions can be added only to draft rules.');
        }

        $persistenceRule = $this->handler->loadRule($rule->getId(), Value::STATUS_DRAFT);

        $this->validator->validateConditionCreateStruct($conditionCreateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $createdCondition = $this->handler->addCondition(
                $persistenceRule,
                new ConditionCreateStruct(
                    array(
                        'type' => $conditionCreateStruct->type,
                        'value' => $conditionCreateStruct->value,
                    )
                )
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
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\ConditionUpdateStruct $conditionUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If condition is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Condition
     */
    public function updateCondition(Condition $condition, APIConditionUpdateStruct $conditionUpdateStruct)
    {
        if ($condition->isPublished()) {
            throw new BadStateException('condition', 'Only draft conditions can be updated.');
        }

        $persistenceCondition = $this->handler->loadCondition($condition->getId(), Value::STATUS_DRAFT);

        $this->validator->validateConditionUpdateStruct($condition, $conditionUpdateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedCondition = $this->handler->updateCondition(
                $persistenceCondition,
                new ConditionUpdateStruct(
                    array(
                        'value' => $conditionUpdateStruct->value,
                    )
                )
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
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If condition is not a draft
     */
    public function deleteCondition(Condition $condition)
    {
        if ($condition->isPublished()) {
            throw new BadStateException('condition', 'Only draft conditions can be deleted.');
        }

        $persistenceCondition = $this->handler->loadCondition($condition->getId(), Value::STATUS_DRAFT);

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
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\RuleCreateStruct
     */
    public function newRuleCreateStruct()
    {
        return $this->structBuilder->newRuleCreateStruct();
    }

    /**
     * Creates a new rule update struct.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\RuleUpdateStruct
     */
    public function newRuleUpdateStruct()
    {
        return $this->structBuilder->newRuleUpdateStruct();
    }

    /**
     * Creates a new rule metadata update struct.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\RuleMetadataUpdateStruct
     */
    public function newRuleMetadataUpdateStruct()
    {
        return $this->structBuilder->newRuleMetadataUpdateStruct();
    }

    /**
     * Creates a new target create struct.
     *
     * @param string $type
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\TargetCreateStruct
     */
    public function newTargetCreateStruct($type)
    {
        return $this->structBuilder->newTargetCreateStruct($type);
    }

    /**
     * Creates a new target update struct.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\TargetUpdateStruct
     */
    public function newTargetUpdateStruct()
    {
        return $this->structBuilder->newTargetUpdateStruct();
    }

    /**
     * Creates a new condition create struct.
     *
     * @param string $type
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\ConditionCreateStruct
     */
    public function newConditionCreateStruct($type)
    {
        return $this->structBuilder->newConditionCreateStruct($type);
    }

    /**
     * Creates a new condition update struct.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\ConditionUpdateStruct
     */
    public function newConditionUpdateStruct()
    {
        return $this->structBuilder->newConditionUpdateStruct();
    }
}
