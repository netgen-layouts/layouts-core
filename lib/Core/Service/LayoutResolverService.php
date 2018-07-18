<?php

declare(strict_types=1);

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
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\HandlerInterface;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition as PersistenceCondition;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule as PersistenceRule;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\RuleCreateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Target as PersistenceTarget;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\TargetCreateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\TargetUpdateStruct;

final class LayoutResolverService extends Service implements APILayoutResolverService
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
     * @var \Netgen\BlockManager\Persistence\Handler\LayoutResolverHandlerInterface
     */
    private $handler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\LayoutHandlerInterface
     */
    private $layoutHandler;

    public function __construct(
        HandlerInterface $persistenceHandler,
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

    public function loadRule($ruleId): Rule
    {
        $this->validator->validateId($ruleId, 'ruleId');

        return $this->mapper->mapRule(
            $this->handler->loadRule(
                $ruleId,
                Value::STATUS_PUBLISHED
            )
        );
    }

    public function loadRuleDraft($ruleId): Rule
    {
        $this->validator->validateId($ruleId, 'ruleId');

        return $this->mapper->mapRule(
            $this->handler->loadRule(
                $ruleId,
                Value::STATUS_DRAFT
            )
        );
    }

    public function loadRuleArchive($ruleId): Rule
    {
        $this->validator->validateId($ruleId, 'ruleId');

        return $this->mapper->mapRule(
            $this->handler->loadRule(
                $ruleId,
                Value::STATUS_ARCHIVED
            )
        );
    }

    public function loadRules(?Layout $layout = null, int $offset = 0, ?int $limit = null): array
    {
        if ($layout instanceof Layout && !$layout->isPublished()) {
            throw new BadStateException('layout', 'Only published layouts can be used in rules.');
        }

        $this->validator->validateOffsetAndLimit($offset, $limit);

        $persistenceLayout = null;
        if ($layout instanceof Layout) {
            $persistenceLayout = $this->layoutHandler->loadLayout(
                $layout->getId(),
                Value::STATUS_PUBLISHED
            );
        }

        $persistenceRules = $this->handler->loadRules(
            Value::STATUS_PUBLISHED,
            $persistenceLayout,
            $offset,
            $limit
        );

        $rules = [];
        foreach ($persistenceRules as $persistenceRule) {
            $rules[] = $this->mapper->mapRule($persistenceRule);
        }

        return $rules;
    }

    public function getRuleCount(?Layout $layout = null): int
    {
        if ($layout instanceof Layout && !$layout->isPublished()) {
            throw new BadStateException('layout', 'Only published layouts can be used in rules.');
        }

        $persistenceLayout = null;
        if ($layout instanceof Layout) {
            $persistenceLayout = $this->layoutHandler->loadLayout(
                $layout->getId(),
                Value::STATUS_PUBLISHED
            );
        }

        return $this->handler->getRuleCount($persistenceLayout);
    }

    public function matchRules(string $targetType, $targetValue): array
    {
        $persistenceRules = $this->handler->matchRules($targetType, $targetValue);

        $rules = [];
        foreach ($persistenceRules as $persistenceRule) {
            $rules[] = $this->mapper->mapRule($persistenceRule);
        }

        return $rules;
    }

    public function loadTarget($targetId): Target
    {
        $this->validator->validateId($targetId, 'targetId');

        return $this->mapper->mapTarget(
            $this->handler->loadTarget(
                $targetId,
                Value::STATUS_PUBLISHED
            )
        );
    }

    public function loadTargetDraft($targetId): Target
    {
        $this->validator->validateId($targetId, 'targetId');

        return $this->mapper->mapTarget(
            $this->handler->loadTarget(
                $targetId,
                Value::STATUS_DRAFT
            )
        );
    }

    public function loadCondition($conditionId): Condition
    {
        $this->validator->validateId($conditionId, 'conditionId');

        return $this->mapper->mapCondition(
            $this->handler->loadCondition(
                $conditionId,
                Value::STATUS_PUBLISHED
            )
        );
    }

    public function loadConditionDraft($conditionId): Condition
    {
        $this->validator->validateId($conditionId, 'conditionId');

        return $this->mapper->mapCondition(
            $this->handler->loadCondition(
                $conditionId,
                Value::STATUS_DRAFT
            )
        );
    }

    public function createRule(APIRuleCreateStruct $ruleCreateStruct): Rule
    {
        $this->validator->validateRuleCreateStruct($ruleCreateStruct);

        $createdRule = $this->transaction(
            function () use ($ruleCreateStruct): PersistenceRule {
                return $this->handler->createRule(
                    RuleCreateStruct::fromArray(
                        [
                            'layoutId' => $ruleCreateStruct->layoutId,
                            'priority' => $ruleCreateStruct->priority,
                            'enabled' => $ruleCreateStruct->enabled,
                            'comment' => $ruleCreateStruct->comment,
                            'status' => Value::STATUS_DRAFT,
                        ]
                    )
                );
            }
        );

        return $this->mapper->mapRule($createdRule);
    }

    public function updateRule(Rule $rule, APIRuleUpdateStruct $ruleUpdateStruct): Rule
    {
        if (!$rule->isDraft()) {
            throw new BadStateException('rule', 'Only draft rules can be updated.');
        }

        $persistenceRule = $this->handler->loadRule($rule->getId(), Value::STATUS_DRAFT);

        $this->validator->validateRuleUpdateStruct($ruleUpdateStruct);

        $updatedRule = $this->transaction(
            function () use ($persistenceRule, $ruleUpdateStruct): PersistenceRule {
                return $this->handler->updateRule(
                    $persistenceRule,
                    RuleUpdateStruct::fromArray(
                        [
                            'layoutId' => $ruleUpdateStruct->layoutId,
                            'comment' => $ruleUpdateStruct->comment,
                        ]
                    )
                );
            }
        );

        return $this->mapper->mapRule($updatedRule);
    }

    public function updateRuleMetadata(Rule $rule, APIRuleMetadataUpdateStruct $ruleUpdateStruct): Rule
    {
        if (!$rule->isPublished()) {
            throw new BadStateException('rule', 'Metadata can be updated only for published rules.');
        }

        $persistenceRule = $this->handler->loadRule($rule->getId(), Value::STATUS_PUBLISHED);

        $this->validator->validateRuleMetadataUpdateStruct($ruleUpdateStruct);

        $updatedRule = $this->transaction(
            function () use ($persistenceRule, $ruleUpdateStruct): PersistenceRule {
                return $this->handler->updateRuleMetadata(
                    $persistenceRule,
                    RuleMetadataUpdateStruct::fromArray(
                        [
                            'priority' => $ruleUpdateStruct->priority,
                        ]
                    )
                );
            }
        );

        return $this->mapper->mapRule($updatedRule);
    }

    public function copyRule(Rule $rule): Rule
    {
        $persistenceRule = $this->handler->loadRule($rule->getId(), $rule->getStatus());

        $copiedRule = $this->transaction(
            function () use ($persistenceRule): PersistenceRule {
                return $this->handler->copyRule($persistenceRule);
            }
        );

        return $this->mapper->mapRule($copiedRule);
    }

    public function createDraft(Rule $rule, bool $discardExisting = false): Rule
    {
        if (!$rule->isPublished()) {
            throw new BadStateException('rule', 'Drafts can only be created from published rules.');
        }

        $persistenceRule = $this->handler->loadRule($rule->getId(), Value::STATUS_PUBLISHED);

        if (!$discardExisting && $this->handler->ruleExists($persistenceRule->id, Value::STATUS_DRAFT)) {
            throw new BadStateException('rule', 'The provided rule already has a draft.');
        }

        $ruleDraft = $this->transaction(
            function () use ($persistenceRule): PersistenceRule {
                $this->handler->deleteRule($persistenceRule->id, Value::STATUS_DRAFT);

                return $this->handler->createRuleStatus($persistenceRule, Value::STATUS_DRAFT);
            }
        );

        return $this->mapper->mapRule($ruleDraft);
    }

    public function discardDraft(Rule $rule): void
    {
        if (!$rule->isDraft()) {
            throw new BadStateException('rule', 'Only draft rules can be discarded.');
        }

        $persistenceRule = $this->handler->loadRule($rule->getId(), Value::STATUS_DRAFT);

        $this->transaction(
            function () use ($persistenceRule): void {
                $this->handler->deleteRule(
                    $persistenceRule->id,
                    Value::STATUS_DRAFT
                );
            }
        );
    }

    public function publishRule(Rule $rule): Rule
    {
        if (!$rule->isDraft()) {
            throw new BadStateException('rule', 'Only draft rules can be published.');
        }

        $persistenceRule = $this->handler->loadRule($rule->getId(), Value::STATUS_DRAFT);

        $publishedRule = $this->transaction(
            function () use ($persistenceRule): PersistenceRule {
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
                        RuleMetadataUpdateStruct::fromArray(
                            [
                                'enabled' => false,
                            ]
                        )
                    );
                }

                return $publishedRule;
            }
        );

        return $this->mapper->mapRule($publishedRule);
    }

    public function restoreFromArchive(Rule $rule): Rule
    {
        if (!$rule->isArchived()) {
            throw new BadStateException('rule', 'Only archived rules can be restored.');
        }

        $archivedRule = $this->handler->loadRule($rule->getId(), Value::STATUS_ARCHIVED);

        $draftRule = null;
        try {
            $draftRule = $this->handler->loadRule($rule->getId(), Value::STATUS_DRAFT);
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $draftRule = $this->transaction(
            function () use ($draftRule, $archivedRule): PersistenceRule {
                if ($draftRule instanceof PersistenceRule) {
                    $this->handler->deleteRule($draftRule->id, $draftRule->status);
                }

                return $this->handler->createRuleStatus($archivedRule, Value::STATUS_DRAFT);
            }
        );

        return $this->mapper->mapRule($draftRule);
    }

    public function deleteRule(Rule $rule): void
    {
        $persistenceRule = $this->handler->loadRule($rule->getId(), $rule->getStatus());

        $this->transaction(
            function () use ($persistenceRule): void {
                $this->handler->deleteRule(
                    $persistenceRule->id
                );
            }
        );
    }

    public function enableRule(Rule $rule): Rule
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
            function () use ($persistenceRule): PersistenceRule {
                return $this->handler->updateRuleMetadata(
                    $persistenceRule,
                    RuleMetadataUpdateStruct::fromArray(
                        [
                            'enabled' => true,
                        ]
                    )
                );
            }
        );

        return $this->mapper->mapRule($updatedRule);
    }

    public function disableRule(Rule $rule): Rule
    {
        if (!$rule->isPublished()) {
            throw new BadStateException('rule', 'Only published rules can be disabled.');
        }

        $persistenceRule = $this->handler->loadRule($rule->getId(), Value::STATUS_PUBLISHED);

        if (!$persistenceRule->enabled) {
            throw new BadStateException('rule', 'Rule is already disabled.');
        }

        $updatedRule = $this->transaction(
            function () use ($persistenceRule): PersistenceRule {
                return $this->handler->updateRuleMetadata(
                    $persistenceRule,
                    RuleMetadataUpdateStruct::fromArray(
                        [
                            'enabled' => false,
                        ]
                    )
                );
            }
        );

        return $this->mapper->mapRule($updatedRule);
    }

    public function addTarget(Rule $rule, APITargetCreateStruct $targetCreateStruct): Target
    {
        if (!$rule->isDraft()) {
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
            function () use ($persistenceRule, $targetCreateStruct): PersistenceTarget {
                return $this->handler->addTarget(
                    $persistenceRule,
                    TargetCreateStruct::fromArray(
                        [
                            'type' => $targetCreateStruct->type,
                            'value' => $targetCreateStruct->value,
                        ]
                    )
                );
            }
        );

        return $this->mapper->mapTarget($createdTarget);
    }

    public function updateTarget(Target $target, APITargetUpdateStruct $targetUpdateStruct): Target
    {
        if (!$target->isDraft()) {
            throw new BadStateException('target', 'Only draft targets can be updated.');
        }

        $persistenceTarget = $this->handler->loadTarget($target->getId(), Value::STATUS_DRAFT);

        $this->validator->validateTargetUpdateStruct($target, $targetUpdateStruct);

        $updatedTarget = $this->transaction(
            function () use ($persistenceTarget, $targetUpdateStruct): PersistenceTarget {
                return $this->handler->updateTarget(
                    $persistenceTarget,
                    TargetUpdateStruct::fromArray(
                        [
                            'value' => $targetUpdateStruct->value,
                        ]
                    )
                );
            }
        );

        return $this->mapper->mapTarget($updatedTarget);
    }

    public function deleteTarget(Target $target): void
    {
        if (!$target->isDraft()) {
            throw new BadStateException('target', 'Only draft targets can be deleted.');
        }

        $persistenceTarget = $this->handler->loadTarget($target->getId(), Value::STATUS_DRAFT);

        $this->transaction(
            function () use ($persistenceTarget): void {
                $this->handler->deleteTarget($persistenceTarget);
            }
        );
    }

    public function addCondition(Rule $rule, APIConditionCreateStruct $conditionCreateStruct): Condition
    {
        if (!$rule->isDraft()) {
            throw new BadStateException('rule', 'Conditions can be added only to draft rules.');
        }

        $persistenceRule = $this->handler->loadRule($rule->getId(), Value::STATUS_DRAFT);

        $this->validator->validateConditionCreateStruct($conditionCreateStruct);

        $createdCondition = $this->transaction(
            function () use ($persistenceRule, $conditionCreateStruct): PersistenceCondition {
                return $this->handler->addCondition(
                    $persistenceRule,
                    ConditionCreateStruct::fromArray(
                        [
                            'type' => $conditionCreateStruct->type,
                            'value' => $conditionCreateStruct->value,
                        ]
                    )
                );
            }
        );

        return $this->mapper->mapCondition($createdCondition);
    }

    public function updateCondition(Condition $condition, APIConditionUpdateStruct $conditionUpdateStruct): Condition
    {
        if (!$condition->isDraft()) {
            throw new BadStateException('condition', 'Only draft conditions can be updated.');
        }

        $persistenceCondition = $this->handler->loadCondition($condition->getId(), Value::STATUS_DRAFT);

        $this->validator->validateConditionUpdateStruct($condition, $conditionUpdateStruct);

        $updatedCondition = $this->transaction(
            function () use ($persistenceCondition, $conditionUpdateStruct): PersistenceCondition {
                return $this->handler->updateCondition(
                    $persistenceCondition,
                    ConditionUpdateStruct::fromArray(
                        [
                            'value' => $conditionUpdateStruct->value,
                        ]
                    )
                );
            }
        );

        return $this->mapper->mapCondition($updatedCondition);
    }

    public function deleteCondition(Condition $condition): void
    {
        if (!$condition->isDraft()) {
            throw new BadStateException('condition', 'Only draft conditions can be deleted.');
        }

        $persistenceCondition = $this->handler->loadCondition($condition->getId(), Value::STATUS_DRAFT);

        $this->transaction(
            function () use ($persistenceCondition): void {
                $this->handler->deleteCondition($persistenceCondition);
            }
        );
    }

    public function newRuleCreateStruct(): APIRuleCreateStruct
    {
        return $this->structBuilder->newRuleCreateStruct();
    }

    public function newRuleUpdateStruct(): APIRuleUpdateStruct
    {
        return $this->structBuilder->newRuleUpdateStruct();
    }

    public function newRuleMetadataUpdateStruct(): APIRuleMetadataUpdateStruct
    {
        return $this->structBuilder->newRuleMetadataUpdateStruct();
    }

    public function newTargetCreateStruct(string $type): APITargetCreateStruct
    {
        return $this->structBuilder->newTargetCreateStruct($type);
    }

    public function newTargetUpdateStruct(): APITargetUpdateStruct
    {
        return $this->structBuilder->newTargetUpdateStruct();
    }

    public function newConditionCreateStruct(string $type): APIConditionCreateStruct
    {
        return $this->structBuilder->newConditionCreateStruct($type);
    }

    public function newConditionUpdateStruct(): APIConditionUpdateStruct
    {
        return $this->structBuilder->newConditionUpdateStruct();
    }
}
