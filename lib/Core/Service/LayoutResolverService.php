<?php

namespace Netgen\BlockManager\Core\Service;

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
    private $validator;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper
     */
    private $mapper;

    /**
     * @var \Netgen\BlockManager\Core\Service\StructBuilder\LayoutResolverStructBuilder
     */
    private $structBuilder;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\LayoutResolverHandler
     */
    private $handler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\LayoutHandler
     */
    private $layoutHandler;

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
        $this->layoutHandler = $persistenceHandler->getLayoutHandler();
    }

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

    public function getRuleCount(Layout $layout)
    {
        if (!$layout->isPublished()) {
            throw new BadStateException('layout', 'Only published layouts can be used in rules.');
        }

        $persistenceLayout = $this->layoutHandler->loadLayout(
            $layout->getId(),
            Value::STATUS_PUBLISHED
        );

        return $this->handler->getRuleCount($persistenceLayout);
    }

    public function matchRules($targetType, $targetValue)
    {
        $persistenceRules = $this->handler->matchRules($targetType, $targetValue);

        $rules = array();
        foreach ($persistenceRules as $persistenceRule) {
            $rules[] = $this->mapper->mapRule($persistenceRule);
        }

        return $rules;
    }

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

    public function createRule(APIRuleCreateStruct $ruleCreateStruct)
    {
        $this->validator->validateRuleCreateStruct($ruleCreateStruct);

        $createdRule = $this->transaction(
            function () use ($ruleCreateStruct) {
                return $this->handler->createRule(
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
            }
        );

        return $this->mapper->mapRule($createdRule);
    }

    public function updateRule(Rule $rule, APIRuleUpdateStruct $ruleUpdateStruct)
    {
        if ($rule->isPublished()) {
            throw new BadStateException('rule', 'Only draft rules can be updated.');
        }

        $persistenceRule = $this->handler->loadRule($rule->getId(), Value::STATUS_DRAFT);

        $this->validator->validateRuleUpdateStruct($ruleUpdateStruct);

        $updatedRule = $this->transaction(
            function () use ($persistenceRule, $ruleUpdateStruct) {
                return $this->handler->updateRule(
                    $persistenceRule,
                    new RuleUpdateStruct(
                        array(
                            'layoutId' => $ruleUpdateStruct->layoutId,
                            'comment' => $ruleUpdateStruct->comment,
                        )
                    )
                );
            }
        );

        return $this->mapper->mapRule($updatedRule);
    }

    public function updateRuleMetadata(Rule $rule, APIRuleMetadataUpdateStruct $ruleUpdateStruct)
    {
        if (!$rule->isPublished()) {
            throw new BadStateException('rule', 'Metadata can be updated only for published rules.');
        }

        $persistenceRule = $this->handler->loadRule($rule->getId(), Value::STATUS_PUBLISHED);

        $this->validator->validateRuleMetadataUpdateStruct($ruleUpdateStruct);

        $updatedRule = $this->transaction(
            function () use ($persistenceRule, $ruleUpdateStruct) {
                return $this->handler->updateRuleMetadata(
                    $persistenceRule,
                    new RuleMetadataUpdateStruct(
                        array(
                            'priority' => $ruleUpdateStruct->priority,
                        )
                    )
                );
            }
        );

        return $this->mapper->mapRule($updatedRule);
    }

    public function copyRule(Rule $rule)
    {
        $persistenceRule = $this->handler->loadRule($rule->getId(), $rule->getStatus());

        $copiedRule = $this->transaction(
            function () use ($persistenceRule) {
                return $this->handler->copyRule($persistenceRule);
            }
        );

        return $this->mapper->mapRule($copiedRule);
    }

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

        $ruleDraft = $this->transaction(
            function () use ($persistenceRule) {
                $this->handler->deleteRule($persistenceRule->id, Value::STATUS_DRAFT);

                return $this->handler->createRuleStatus($persistenceRule, Value::STATUS_DRAFT);
            }
        );

        return $this->mapper->mapRule($ruleDraft);
    }

    public function discardDraft(Rule $rule)
    {
        if ($rule->isPublished()) {
            throw new BadStateException('rule', 'Only draft rules can be discarded.');
        }

        $persistenceRule = $this->handler->loadRule($rule->getId(), Value::STATUS_DRAFT);

        $this->transaction(
            function () use ($persistenceRule) {
                $this->handler->deleteRule(
                    $persistenceRule->id,
                    Value::STATUS_DRAFT
                );
            }
        );
    }

    public function publishRule(Rule $rule)
    {
        if ($rule->isPublished()) {
            throw new BadStateException('rule', 'Only draft rules can be published.');
        }

        $persistenceRule = $this->handler->loadRule($rule->getId(), Value::STATUS_DRAFT);

        $publishedRule = $this->transaction(
            function () use ($persistenceRule) {
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

                return $publishedRule;
            }
        );

        return $this->mapper->mapRule($publishedRule);
    }

    public function deleteRule(Rule $rule)
    {
        $persistenceRule = $this->handler->loadRule($rule->getId(), $rule->getStatus());

        $this->transaction(
            function () use ($persistenceRule) {
                $this->handler->deleteRule(
                    $persistenceRule->id
                );
            }
        );
    }

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

        $updatedRule = $this->transaction(
            function () use ($persistenceRule) {
                return $this->handler->updateRuleMetadata(
                    $persistenceRule,
                    new RuleMetadataUpdateStruct(
                        array(
                            'enabled' => true,
                        )
                    )
                );
            }
        );

        return $this->mapper->mapRule($updatedRule);
    }

    public function disableRule(Rule $rule)
    {
        if (!$rule->isPublished()) {
            throw new BadStateException('rule', 'Only published rules can be disabled.');
        }

        $persistenceRule = $this->handler->loadRule($rule->getId(), Value::STATUS_PUBLISHED);

        if (!$persistenceRule->enabled) {
            throw new BadStateException('rule', 'Rule is already disabled.');
        }

        $updatedRule = $this->transaction(
            function () use ($persistenceRule) {
                return $this->handler->updateRuleMetadata(
                    $persistenceRule,
                    new RuleMetadataUpdateStruct(
                        array(
                            'enabled' => false,
                        )
                    )
                );
            }
        );

        return $this->mapper->mapRule($updatedRule);
    }

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

        $createdTarget = $this->transaction(
            function () use ($persistenceRule, $targetCreateStruct) {
                return $this->handler->addTarget(
                    $persistenceRule,
                    new TargetCreateStruct(
                        array(
                            'type' => $targetCreateStruct->type,
                            'value' => $targetCreateStruct->value,
                        )
                    )
                );
            }
        );

        return $this->mapper->mapTarget($createdTarget);
    }

    public function updateTarget(Target $target, APITargetUpdateStruct $targetUpdateStruct)
    {
        if ($target->isPublished()) {
            throw new BadStateException('target', 'Only draft targets can be updated.');
        }

        $persistenceTarget = $this->handler->loadTarget($target->getId(), Value::STATUS_DRAFT);

        $this->validator->validateTargetUpdateStruct($target, $targetUpdateStruct);

        $updatedTarget = $this->transaction(
            function () use ($persistenceTarget, $targetUpdateStruct) {
                return $this->handler->updateTarget(
                    $persistenceTarget,
                    new TargetUpdateStruct(
                        array(
                            'value' => $targetUpdateStruct->value,
                        )
                    )
                );
            }
        );

        return $this->mapper->mapTarget($updatedTarget);
    }

    public function deleteTarget(Target $target)
    {
        if ($target->isPublished()) {
            throw new BadStateException('target', 'Only draft targets can be deleted.');
        }

        $persistenceTarget = $this->handler->loadTarget($target->getId(), Value::STATUS_DRAFT);

        $this->transaction(
            function () use ($persistenceTarget) {
                $this->handler->deleteTarget($persistenceTarget);
            }
        );
    }

    public function addCondition(Rule $rule, APIConditionCreateStruct $conditionCreateStruct)
    {
        if ($rule->isPublished()) {
            throw new BadStateException('rule', 'Conditions can be added only to draft rules.');
        }

        $persistenceRule = $this->handler->loadRule($rule->getId(), Value::STATUS_DRAFT);

        $this->validator->validateConditionCreateStruct($conditionCreateStruct);

        $createdCondition = $this->transaction(
            function () use ($persistenceRule, $conditionCreateStruct) {
                return $this->handler->addCondition(
                    $persistenceRule,
                    new ConditionCreateStruct(
                        array(
                            'type' => $conditionCreateStruct->type,
                            'value' => $conditionCreateStruct->value,
                        )
                    )
                );
            }
        );

        return $this->mapper->mapCondition($createdCondition);
    }

    public function updateCondition(Condition $condition, APIConditionUpdateStruct $conditionUpdateStruct)
    {
        if ($condition->isPublished()) {
            throw new BadStateException('condition', 'Only draft conditions can be updated.');
        }

        $persistenceCondition = $this->handler->loadCondition($condition->getId(), Value::STATUS_DRAFT);

        $this->validator->validateConditionUpdateStruct($condition, $conditionUpdateStruct);

        $updatedCondition = $this->transaction(
            function () use ($persistenceCondition, $conditionUpdateStruct) {
                return $this->handler->updateCondition(
                    $persistenceCondition,
                    new ConditionUpdateStruct(
                        array(
                            'value' => $conditionUpdateStruct->value,
                        )
                    )
                );
            }
        );

        return $this->mapper->mapCondition($updatedCondition);
    }

    public function deleteCondition(Condition $condition)
    {
        if ($condition->isPublished()) {
            throw new BadStateException('condition', 'Only draft conditions can be deleted.');
        }

        $persistenceCondition = $this->handler->loadCondition($condition->getId(), Value::STATUS_DRAFT);

        $this->transaction(
            function () use ($persistenceCondition) {
                $this->handler->deleteCondition($persistenceCondition);
            }
        );
    }

    public function newRuleCreateStruct()
    {
        return $this->structBuilder->newRuleCreateStruct();
    }

    public function newRuleUpdateStruct()
    {
        return $this->structBuilder->newRuleUpdateStruct();
    }

    public function newRuleMetadataUpdateStruct()
    {
        return $this->structBuilder->newRuleMetadataUpdateStruct();
    }

    public function newTargetCreateStruct($type)
    {
        return $this->structBuilder->newTargetCreateStruct($type);
    }

    public function newTargetUpdateStruct()
    {
        return $this->structBuilder->newTargetUpdateStruct();
    }

    public function newConditionCreateStruct($type)
    {
        return $this->structBuilder->newConditionCreateStruct($type);
    }

    public function newConditionUpdateStruct()
    {
        return $this->structBuilder->newConditionUpdateStruct();
    }
}
