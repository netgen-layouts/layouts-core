<?php

declare(strict_types=1);

namespace Netgen\Layouts\Core\Service;

use Netgen\Layouts\API\Service\LayoutResolverService as APILayoutResolverService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Condition;
use Netgen\Layouts\API\Values\LayoutResolver\ConditionCreateStruct as APIConditionCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\ConditionUpdateStruct as APIConditionUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCreateStruct as APIRuleCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupCondition;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupCreateStruct as APIRuleGroupCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupList;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupMetadataUpdateStruct as APIRuleGroupMetadataUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupUpdateStruct as APIRuleGroupUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleList;
use Netgen\Layouts\API\Values\LayoutResolver\RuleMetadataUpdateStruct as APIRuleMetadataUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleUpdateStruct as APIRuleUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\API\Values\LayoutResolver\TargetCreateStruct as APITargetCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\TargetUpdateStruct as APITargetUpdateStruct;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\Core\Mapper\LayoutResolverMapper;
use Netgen\Layouts\Core\StructBuilder\LayoutResolverStructBuilder;
use Netgen\Layouts\Core\Validator\LayoutResolverValidator;
use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Persistence\Handler\LayoutHandlerInterface;
use Netgen\Layouts\Persistence\Handler\LayoutResolverHandlerInterface;
use Netgen\Layouts\Persistence\TransactionHandlerInterface;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Condition as PersistenceCondition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Rule as PersistenceRule;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleCondition as PersistenceRuleCondition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleCreateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroup as PersistenceRuleGroup;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroupCondition as PersistenceRuleGroupCondition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroupCreateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroupMetadataUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroupUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Target as PersistenceTarget;
use Netgen\Layouts\Persistence\Values\LayoutResolver\TargetCreateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\TargetUpdateStruct;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

use function array_map;
use function count;
use function sprintf;
use function trigger_deprecation;
use function trim;

final class LayoutResolverService implements APILayoutResolverService
{
    use TransactionTrait;

    private LayoutResolverValidator $validator;

    private LayoutResolverMapper $mapper;

    private LayoutResolverStructBuilder $structBuilder;

    private LayoutResolverHandlerInterface $layoutResolverHandler;

    private LayoutHandlerInterface $layoutHandler;

    public function __construct(
        TransactionHandlerInterface $transactionHandler,
        LayoutResolverValidator $validator,
        LayoutResolverMapper $mapper,
        LayoutResolverStructBuilder $structBuilder,
        LayoutResolverHandlerInterface $layoutResolverHandler,
        LayoutHandlerInterface $layoutHandler
    ) {
        $this->transactionHandler = $transactionHandler;

        $this->validator = $validator;
        $this->mapper = $mapper;
        $this->structBuilder = $structBuilder;
        $this->layoutResolverHandler = $layoutResolverHandler;
        $this->layoutHandler = $layoutHandler;
    }

    public function loadRule(UuidInterface $ruleId): Rule
    {
        return $this->mapper->mapRule(
            $this->layoutResolverHandler->loadRule(
                $ruleId,
                Value::STATUS_PUBLISHED,
            ),
        );
    }

    public function loadRuleDraft(UuidInterface $ruleId): Rule
    {
        return $this->mapper->mapRule(
            $this->layoutResolverHandler->loadRule(
                $ruleId,
                Value::STATUS_DRAFT,
            ),
        );
    }

    public function loadRuleArchive(UuidInterface $ruleId): Rule
    {
        return $this->mapper->mapRule(
            $this->layoutResolverHandler->loadRule(
                $ruleId,
                Value::STATUS_ARCHIVED,
            ),
        );
    }

    public function loadRuleGroup(UuidInterface $ruleGroupId): RuleGroup
    {
        return $this->mapper->mapRuleGroup(
            $this->layoutResolverHandler->loadRuleGroup(
                $ruleGroupId,
                Value::STATUS_PUBLISHED,
            ),
        );
    }

    public function loadRuleGroupDraft(UuidInterface $ruleGroupId): RuleGroup
    {
        return $this->mapper->mapRuleGroup(
            $this->layoutResolverHandler->loadRuleGroup(
                $ruleGroupId,
                Value::STATUS_DRAFT,
            ),
        );
    }

    public function loadRuleGroupArchive(UuidInterface $ruleGroupId): RuleGroup
    {
        return $this->mapper->mapRuleGroup(
            $this->layoutResolverHandler->loadRuleGroup(
                $ruleGroupId,
                Value::STATUS_ARCHIVED,
            ),
        );
    }

    public function loadRules(?Layout $layout = null, int $offset = 0, ?int $limit = null): RuleList
    {
        if ($layout === null) {
            return $this->loadRulesFromGroup(
                $this->loadRuleGroup(Uuid::fromString(RuleGroup::ROOT_UUID)),
                $offset,
                $limit,
            );
        }

        return $this->loadRulesForLayout($layout, $offset, $limit);
    }

    public function loadRulesForLayout(Layout $layout, int $offset = 0, ?int $limit = null, bool $ascending = false): RuleList
    {
        if (!$layout->isPublished()) {
            throw new BadStateException('layout', 'Only published layouts can be used in rules.');
        }

        $persistenceLayout = $this->layoutHandler->loadLayout(
            $layout->getId(),
            Value::STATUS_PUBLISHED,
        );

        $persistenceRules = $this->layoutResolverHandler->loadRulesForLayout(
            $persistenceLayout,
            $offset,
            $limit,
            $ascending,
        );

        return new RuleList(
            array_map(
                fn (PersistenceRule $rule): Rule => $this->mapper->mapRule($rule),
                $persistenceRules,
            ),
        );
    }

    public function getRuleCount(?Layout $layout = null): int
    {
        if ($layout === null) {
            return $this->getRuleCountFromGroup(
                $this->loadRuleGroup(Uuid::fromString(RuleGroup::ROOT_UUID)),
            );
        }

        return $this->getRuleCountForLayout($layout);
    }

    public function getRuleCountForLayout(Layout $layout): int
    {
        if (!$layout->isPublished()) {
            throw new BadStateException('layout', 'Only published layouts can be used in rules.');
        }

        $persistenceLayout = $this->layoutHandler->loadLayout(
            $layout->getId(),
            Value::STATUS_PUBLISHED,
        );

        return $this->layoutResolverHandler->getRuleCountForLayout($persistenceLayout);
    }

    public function loadRulesFromGroup(RuleGroup $ruleGroup, int $offset = 0, ?int $limit = null, bool $ascending = false): RuleList
    {
        if (!$ruleGroup->isPublished()) {
            throw new BadStateException('ruleGroup', 'Rules can be loaded only from published rule groups.');
        }

        $persistenceGroup = $this->layoutResolverHandler->loadRuleGroup(
            $ruleGroup->getId(),
            Value::STATUS_PUBLISHED,
        );

        $persistenceRules = $this->layoutResolverHandler->loadRulesFromGroup(
            $persistenceGroup,
            $offset,
            $limit,
            $ascending,
        );

        return new RuleList(
            array_map(
                fn (PersistenceRule $rule): Rule => $this->mapper->mapRule($rule),
                $persistenceRules,
            ),
        );
    }

    public function getRuleCountFromGroup(RuleGroup $ruleGroup): int
    {
        if (!$ruleGroup->isPublished()) {
            throw new BadStateException('ruleGroup', 'Rule count can be fetched only for published rule groups.');
        }

        $persistenceGroup = $this->layoutResolverHandler->loadRuleGroup(
            $ruleGroup->getId(),
            Value::STATUS_PUBLISHED,
        );

        return $this->layoutResolverHandler->getRuleCountFromGroup($persistenceGroup);
    }

    public function loadRuleGroups(RuleGroup $parentGroup, int $offset = 0, ?int $limit = null, bool $ascending = false): RuleGroupList
    {
        if (!$parentGroup->isPublished()) {
            throw new BadStateException('parentGroup', 'Rule groups can be loaded only from published parent groups.');
        }

        $persistenceParentGroup = $this->layoutResolverHandler->loadRuleGroup(
            $parentGroup->getId(),
            Value::STATUS_PUBLISHED,
        );

        $persistenceRuleGroups = $this->layoutResolverHandler->loadRuleGroups(
            $persistenceParentGroup,
            $offset,
            $limit,
            $ascending,
        );

        return new RuleGroupList(
            array_map(
                fn (PersistenceRuleGroup $ruleGroup): RuleGroup => $this->mapper->mapRuleGroup($ruleGroup),
                $persistenceRuleGroups,
            ),
        );
    }

    public function getRuleGroupCount(RuleGroup $parentGroup): int
    {
        if (!$parentGroup->isPublished()) {
            throw new BadStateException('parentGroup', 'Rule group count can be fetched only for published parent groups.');
        }

        $persistenceParentGroup = $this->layoutResolverHandler->loadRuleGroup(
            $parentGroup->getId(),
            Value::STATUS_PUBLISHED,
        );

        return $this->layoutResolverHandler->getRuleGroupCount($persistenceParentGroup);
    }

    public function matchRules(RuleGroup $ruleGroup, string $targetType, $targetValue): RuleList
    {
        $persistenceGroup = $this->layoutResolverHandler->loadRuleGroup(
            $ruleGroup->getId(),
            $ruleGroup->getStatus(),
        );

        return new RuleList(
            array_map(
                fn (PersistenceRule $rule): Rule => $this->mapper->mapRule($rule),
                $this->layoutResolverHandler->matchRules($persistenceGroup, $targetType, $targetValue),
            ),
        );
    }

    public function loadTarget(UuidInterface $targetId): Target
    {
        return $this->mapper->mapTarget(
            $this->layoutResolverHandler->loadTarget(
                $targetId,
                Value::STATUS_PUBLISHED,
            ),
        );
    }

    public function loadTargetDraft(UuidInterface $targetId): Target
    {
        return $this->mapper->mapTarget(
            $this->layoutResolverHandler->loadTarget(
                $targetId,
                Value::STATUS_DRAFT,
            ),
        );
    }

    public function loadCondition(UuidInterface $conditionId): RuleCondition
    {
        return $this->loadRuleCondition($conditionId);
    }

    public function loadRuleCondition(UuidInterface $conditionId): RuleCondition
    {
        return $this->mapper->mapRuleCondition(
            $this->layoutResolverHandler->loadRuleCondition(
                $conditionId,
                Value::STATUS_PUBLISHED,
            ),
        );
    }

    public function loadConditionDraft(UuidInterface $conditionId): RuleCondition
    {
        return $this->loadRuleConditionDraft($conditionId);
    }

    public function loadRuleConditionDraft(UuidInterface $conditionId): RuleCondition
    {
        return $this->mapper->mapRuleCondition(
            $this->layoutResolverHandler->loadRuleCondition(
                $conditionId,
                Value::STATUS_DRAFT,
            ),
        );
    }

    public function loadRuleGroupCondition(UuidInterface $conditionId): RuleGroupCondition
    {
        return $this->mapper->mapRuleGroupCondition(
            $this->layoutResolverHandler->loadRuleGroupCondition(
                $conditionId,
                Value::STATUS_PUBLISHED,
            ),
        );
    }

    public function loadRuleGroupConditionDraft(UuidInterface $conditionId): RuleGroupCondition
    {
        return $this->mapper->mapRuleGroupCondition(
            $this->layoutResolverHandler->loadRuleGroupCondition(
                $conditionId,
                Value::STATUS_DRAFT,
            ),
        );
    }

    public function ruleExists(UuidInterface $ruleId, ?int $status = null): bool
    {
        return $this->layoutResolverHandler->ruleExists($ruleId, $status);
    }

    public function createRule(APIRuleCreateStruct $ruleCreateStruct, RuleGroup $targetGroup): Rule
    {
        if (!$targetGroup->isPublished()) {
            throw new BadStateException('targetGroup', 'Rules can be created only in published groups.');
        }

        $description = '';
        if (trim($ruleCreateStruct->description) !== '') {
            $description = $ruleCreateStruct->description;
        } elseif ($ruleCreateStruct->comment !== null && trim($ruleCreateStruct->comment) !== '') {
            trigger_deprecation('netgen/layouts-core', '1.3', sprintf('Using %s::$comment property is deprecated. Use RuleCreateStruct::$description instead.', APIRuleCreateStruct::class));

            $description = $ruleCreateStruct->comment;
        }

        $createdRule = $this->transaction(
            fn (): PersistenceRule => $this->layoutResolverHandler->createRule(
                RuleCreateStruct::fromArray(
                    [
                        'uuid' => $ruleCreateStruct->uuid instanceof UuidInterface ?
                            $ruleCreateStruct->uuid->toString() :
                            $ruleCreateStruct->uuid,
                        'layoutId' => $ruleCreateStruct->layoutId instanceof UuidInterface ?
                            $ruleCreateStruct->layoutId->toString() :
                            $ruleCreateStruct->layoutId,
                        'priority' => $ruleCreateStruct->priority,
                        'enabled' => $ruleCreateStruct->enabled,
                        'description' => $description,
                        'status' => Value::STATUS_DRAFT,
                    ],
                ),
                $this->layoutResolverHandler->loadRuleGroup($targetGroup->getId(), Value::STATUS_PUBLISHED),
            ),
        );

        return $this->mapper->mapRule($createdRule);
    }

    public function updateRule(Rule $rule, APIRuleUpdateStruct $ruleUpdateStruct): Rule
    {
        if (!$rule->isDraft()) {
            throw new BadStateException('rule', 'Only draft rules can be updated.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->getId(), Value::STATUS_DRAFT);

        $this->validator->validateRuleUpdateStruct($ruleUpdateStruct);

        $updatedRule = $this->transaction(
            fn (): PersistenceRule => $this->layoutResolverHandler->updateRule(
                $persistenceRule,
                RuleUpdateStruct::fromArray(
                    [
                        'layoutId' => $ruleUpdateStruct->layoutId instanceof UuidInterface ?
                            $ruleUpdateStruct->layoutId->toString() :
                            $ruleUpdateStruct->layoutId,
                        'description' => $ruleUpdateStruct->description ?? $ruleUpdateStruct->comment,
                    ],
                ),
            ),
        );

        return $this->mapper->mapRule($updatedRule);
    }

    public function updateRuleMetadata(Rule $rule, APIRuleMetadataUpdateStruct $ruleUpdateStruct): Rule
    {
        if (!$rule->isPublished()) {
            throw new BadStateException('rule', 'Metadata can be updated only for published rules.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->getId(), Value::STATUS_PUBLISHED);

        $updatedRule = $this->transaction(
            fn (): PersistenceRule => $this->layoutResolverHandler->updateRuleMetadata(
                $persistenceRule,
                RuleMetadataUpdateStruct::fromArray(
                    [
                        'priority' => $ruleUpdateStruct->priority,
                    ],
                ),
            ),
        );

        return $this->mapper->mapRule($updatedRule);
    }

    public function copyRule(Rule $rule, RuleGroup $targetGroup): Rule
    {
        if (!$rule->isPublished()) {
            throw new BadStateException('rule', 'Only published rules can be copied.');
        }

        if (!$targetGroup->isPublished()) {
            throw new BadStateException('targetGroup', 'Rules can be copied only to published groups.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->getId(), Value::STATUS_PUBLISHED);
        $persistenceGroup = $this->layoutResolverHandler->loadRuleGroup($targetGroup->getId(), Value::STATUS_PUBLISHED);

        $copiedRule = $this->transaction(
            fn (): PersistenceRule => $this->layoutResolverHandler->copyRule($persistenceRule, $persistenceGroup),
        );

        return $this->mapper->mapRule($copiedRule);
    }

    public function moveRule(Rule $rule, RuleGroup $targetGroup, ?int $newPriority = null): Rule
    {
        if (!$rule->isPublished()) {
            throw new BadStateException('rule', 'Only published rules can be moved.');
        }

        if (!$targetGroup->isPublished()) {
            throw new BadStateException('targetGroup', 'Rules can be moved only to published groups.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->getId(), Value::STATUS_PUBLISHED);
        $persistenceTargetGroup = $this->layoutResolverHandler->loadRuleGroup($targetGroup->getId(), Value::STATUS_PUBLISHED);

        $movedRule = $this->transaction(
            fn (): PersistenceRule => $this->layoutResolverHandler->moveRule(
                $persistenceRule,
                $persistenceTargetGroup,
                $newPriority,
            ),
        );

        return $this->mapper->mapRule($movedRule);
    }

    public function createDraft(Rule $rule, bool $discardExisting = false): Rule
    {
        return $this->createRuleDraft($rule, $discardExisting);
    }

    public function createRuleDraft(Rule $rule, bool $discardExisting = false): Rule
    {
        if (!$rule->isPublished()) {
            throw new BadStateException('rule', 'Drafts can only be created from published rules.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->getId(), Value::STATUS_PUBLISHED);

        if (!$discardExisting && $this->layoutResolverHandler->ruleExists($persistenceRule->id, Value::STATUS_DRAFT)) {
            throw new BadStateException('rule', 'The provided rule already has a draft.');
        }

        $ruleDraft = $this->transaction(
            function () use ($persistenceRule): PersistenceRule {
                $this->layoutResolverHandler->deleteRule($persistenceRule->id, Value::STATUS_DRAFT);

                return $this->layoutResolverHandler->createRuleStatus($persistenceRule, Value::STATUS_DRAFT);
            },
        );

        return $this->mapper->mapRule($ruleDraft);
    }

    public function discardDraft(Rule $rule): void
    {
        $this->discardRuleDraft($rule);
    }

    public function discardRuleDraft(Rule $rule): void
    {
        if (!$rule->isDraft()) {
            throw new BadStateException('rule', 'Only draft rules can be discarded.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->getId(), Value::STATUS_DRAFT);

        $this->transaction(
            function () use ($persistenceRule): void {
                $this->layoutResolverHandler->deleteRule(
                    $persistenceRule->id,
                    Value::STATUS_DRAFT,
                );
            },
        );
    }

    public function publishRule(Rule $rule): Rule
    {
        if (!$rule->isDraft()) {
            throw new BadStateException('rule', 'Only draft rules can be published.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->getId(), Value::STATUS_DRAFT);

        $publishedRule = $this->transaction(
            function () use ($persistenceRule): PersistenceRule {
                $this->layoutResolverHandler->deleteRule($persistenceRule->id, Value::STATUS_ARCHIVED);

                if ($this->layoutResolverHandler->ruleExists($persistenceRule->id, Value::STATUS_PUBLISHED)) {
                    $this->layoutResolverHandler->createRuleStatus(
                        $this->layoutResolverHandler->loadRule(
                            $persistenceRule->id,
                            Value::STATUS_PUBLISHED,
                        ),
                        Value::STATUS_ARCHIVED,
                    );

                    $this->layoutResolverHandler->deleteRule($persistenceRule->id, Value::STATUS_PUBLISHED);
                }

                $publishedRule = $this->layoutResolverHandler->createRuleStatus($persistenceRule, Value::STATUS_PUBLISHED);
                $this->layoutResolverHandler->deleteRule($persistenceRule->id, Value::STATUS_DRAFT);

                return $publishedRule;
            },
        );

        return $this->mapper->mapRule($publishedRule);
    }

    public function restoreFromArchive(Rule $rule): Rule
    {
        return $this->restoreRuleFromArchive($rule);
    }

    public function restoreRuleFromArchive(Rule $rule): Rule
    {
        if (!$rule->isArchived()) {
            throw new BadStateException('rule', 'Only archived rules can be restored.');
        }

        $archivedRule = $this->layoutResolverHandler->loadRule($rule->getId(), Value::STATUS_ARCHIVED);

        $draftRule = null;

        try {
            $draftRule = $this->layoutResolverHandler->loadRule($rule->getId(), Value::STATUS_DRAFT);
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $draftRule = $this->transaction(
            function () use ($draftRule, $archivedRule): PersistenceRule {
                if ($draftRule instanceof PersistenceRule) {
                    $this->layoutResolverHandler->deleteRule($draftRule->id, $draftRule->status);
                }

                return $this->layoutResolverHandler->createRuleStatus($archivedRule, Value::STATUS_DRAFT);
            },
        );

        return $this->mapper->mapRule($draftRule);
    }

    public function deleteRule(Rule $rule): void
    {
        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->getId(), $rule->getStatus());

        $this->transaction(
            function () use ($persistenceRule): void {
                $this->layoutResolverHandler->deleteRule(
                    $persistenceRule->id,
                );
            },
        );
    }

    public function ruleGroupExists(UuidInterface $ruleGroupId, ?int $status = null): bool
    {
        return $this->layoutResolverHandler->ruleGroupExists($ruleGroupId, $status);
    }

    public function createRuleGroup(APIRuleGroupCreateStruct $ruleGroupCreateStruct, ?RuleGroup $parentGroup = null): RuleGroup
    {
        $this->validator->validateRuleGroupCreateStruct($ruleGroupCreateStruct);

        if ($parentGroup !== null && !$parentGroup->isPublished()) {
            throw new BadStateException('parentGroup', 'Rule groups can be created only in published groups.');
        }

        $createdRuleGroup = $this->transaction(
            fn (): PersistenceRuleGroup => $this->layoutResolverHandler->createRuleGroup(
                RuleGroupCreateStruct::fromArray(
                    [
                        'uuid' => $ruleGroupCreateStruct->uuid instanceof UuidInterface ?
                            $ruleGroupCreateStruct->uuid->toString() :
                            $ruleGroupCreateStruct->uuid,
                        'name' => $ruleGroupCreateStruct->name,
                        'description' => $ruleGroupCreateStruct->description,
                        'priority' => $ruleGroupCreateStruct->priority,
                        'enabled' => $ruleGroupCreateStruct->enabled,
                        'status' => Value::STATUS_DRAFT,
                    ],
                ),
                $parentGroup !== null ?
                    $this->layoutResolverHandler->loadRuleGroup($parentGroup->getId(), Value::STATUS_PUBLISHED) :
                    null,
            ),
        );

        return $this->mapper->mapRuleGroup($createdRuleGroup);
    }

    public function updateRuleGroup(RuleGroup $ruleGroup, APIRuleGroupUpdateStruct $ruleGroupUpdateStruct): RuleGroup
    {
        if (!$ruleGroup->isDraft()) {
            throw new BadStateException('ruleGroup', 'Only draft rule groups can be updated.');
        }

        $persistenceRuleGroup = $this->layoutResolverHandler->loadRuleGroup($ruleGroup->getId(), Value::STATUS_DRAFT);

        $this->validator->validateRuleGroupUpdateStruct($ruleGroupUpdateStruct);

        $updatedRuleGroup = $this->transaction(
            fn (): PersistenceRuleGroup => $this->layoutResolverHandler->updateRuleGroup(
                $persistenceRuleGroup,
                RuleGroupUpdateStruct::fromArray(
                    [
                        'name' => $ruleGroupUpdateStruct->name,
                        'description' => $ruleGroupUpdateStruct->description,
                    ],
                ),
            ),
        );

        return $this->mapper->mapRuleGroup($updatedRuleGroup);
    }

    public function updateRuleGroupMetadata(RuleGroup $ruleGroup, APIRuleGroupMetadataUpdateStruct $ruleGroupUpdateStruct): RuleGroup
    {
        if (!$ruleGroup->isPublished()) {
            throw new BadStateException('ruleGroup', 'Metadata can be updated only for published rule groups.');
        }

        $persistenceRuleGroup = $this->layoutResolverHandler->loadRuleGroup($ruleGroup->getId(), Value::STATUS_PUBLISHED);

        $updatedRuleGroup = $this->transaction(
            fn (): PersistenceRuleGroup => $this->layoutResolverHandler->updateRuleGroupMetadata(
                $persistenceRuleGroup,
                RuleGroupMetadataUpdateStruct::fromArray(
                    [
                        'priority' => $ruleGroupUpdateStruct->priority,
                    ],
                ),
            ),
        );

        return $this->mapper->mapRuleGroup($updatedRuleGroup);
    }

    public function copyRuleGroup(RuleGroup $ruleGroup, RuleGroup $targetGroup, bool $copyChildren = false): RuleGroup
    {
        if (!$ruleGroup->isPublished()) {
            throw new BadStateException('ruleGroup', 'Only published rule groups can be copied.');
        }

        if (!$targetGroup->isPublished()) {
            throw new BadStateException('targetGroup', 'Rule groups can be copied only to published groups.');
        }

        $persistenceRuleGroup = $this->layoutResolverHandler->loadRuleGroup($ruleGroup->getId(), Value::STATUS_PUBLISHED);
        $persistenceTargetGroup = $this->layoutResolverHandler->loadRuleGroup($targetGroup->getId(), Value::STATUS_PUBLISHED);

        $copiedRuleGroup = $this->transaction(
            fn (): PersistenceRuleGroup => $this->layoutResolverHandler->copyRuleGroup($persistenceRuleGroup, $persistenceTargetGroup, $copyChildren),
        );

        return $this->mapper->mapRuleGroup($copiedRuleGroup);
    }

    public function moveRuleGroup(RuleGroup $ruleGroup, RuleGroup $targetGroup, ?int $newPriority = null): RuleGroup
    {
        if (!$ruleGroup->isPublished()) {
            throw new BadStateException('ruleGroup', 'Only published rule groups can be moved.');
        }

        if (!$targetGroup->isPublished()) {
            throw new BadStateException('targetGroup', 'Rule groups can be moved only to published groups.');
        }

        $persistenceRuleGroup = $this->layoutResolverHandler->loadRuleGroup($ruleGroup->getId(), Value::STATUS_PUBLISHED);
        $persistenceTargetGroup = $this->layoutResolverHandler->loadRuleGroup($targetGroup->getId(), Value::STATUS_PUBLISHED);

        $movedRuleGroup = $this->transaction(
            fn (): PersistenceRuleGroup => $this->layoutResolverHandler->moveRuleGroup(
                $persistenceRuleGroup,
                $persistenceTargetGroup,
                $newPriority,
            ),
        );

        return $this->mapper->mapRuleGroup($movedRuleGroup);
    }

    public function createRuleGroupDraft(RuleGroup $ruleGroup, bool $discardExisting = false): RuleGroup
    {
        if (!$ruleGroup->isPublished()) {
            throw new BadStateException('ruleGroup', 'Drafts can only be created from published rule groups.');
        }

        $persistenceRuleGroup = $this->layoutResolverHandler->loadRuleGroup($ruleGroup->getId(), Value::STATUS_PUBLISHED);

        if (!$discardExisting && $this->layoutResolverHandler->ruleGroupExists($persistenceRuleGroup->id, Value::STATUS_DRAFT)) {
            throw new BadStateException('ruleGroup', 'The provided rule group already has a draft.');
        }

        $ruleGroupDraft = $this->transaction(
            function () use ($persistenceRuleGroup): PersistenceRuleGroup {
                $this->layoutResolverHandler->deleteRuleGroup($persistenceRuleGroup->id, Value::STATUS_DRAFT);

                return $this->layoutResolverHandler->createRuleGroupStatus($persistenceRuleGroup, Value::STATUS_DRAFT);
            },
        );

        return $this->mapper->mapRuleGroup($ruleGroupDraft);
    }

    public function discardRuleGroupDraft(RuleGroup $ruleGroup): void
    {
        if (!$ruleGroup->isDraft()) {
            throw new BadStateException('ruleGroup', 'Only draft rule groups can be discarded.');
        }

        $persistenceRuleGroup = $this->layoutResolverHandler->loadRuleGroup($ruleGroup->getId(), Value::STATUS_DRAFT);

        $this->transaction(
            function () use ($persistenceRuleGroup): void {
                $this->layoutResolverHandler->deleteRuleGroup(
                    $persistenceRuleGroup->id,
                    Value::STATUS_DRAFT,
                );
            },
        );
    }

    public function publishRuleGroup(RuleGroup $ruleGroup): RuleGroup
    {
        if (!$ruleGroup->isDraft()) {
            throw new BadStateException('ruleGroup', 'Only draft rule groups can be published.');
        }

        $persistenceRuleGroup = $this->layoutResolverHandler->loadRuleGroup($ruleGroup->getId(), Value::STATUS_DRAFT);

        $publishedRuleGroup = $this->transaction(
            function () use ($persistenceRuleGroup): PersistenceRuleGroup {
                $this->layoutResolverHandler->deleteRuleGroup($persistenceRuleGroup->id, Value::STATUS_ARCHIVED);

                if ($this->layoutResolverHandler->ruleGroupExists($persistenceRuleGroup->id, Value::STATUS_PUBLISHED)) {
                    $this->layoutResolverHandler->createRuleGroupStatus(
                        $this->layoutResolverHandler->loadRuleGroup(
                            $persistenceRuleGroup->id,
                            Value::STATUS_PUBLISHED,
                        ),
                        Value::STATUS_ARCHIVED,
                    );

                    $this->layoutResolverHandler->deleteRuleGroup($persistenceRuleGroup->id, Value::STATUS_PUBLISHED);
                }

                $publishedRuleGroup = $this->layoutResolverHandler->createRuleGroupStatus($persistenceRuleGroup, Value::STATUS_PUBLISHED);
                $this->layoutResolverHandler->deleteRuleGroup($persistenceRuleGroup->id, Value::STATUS_DRAFT);

                return $publishedRuleGroup;
            },
        );

        return $this->mapper->mapRuleGroup($publishedRuleGroup);
    }

    public function restoreRuleGroupFromArchive(RuleGroup $ruleGroup): RuleGroup
    {
        if (!$ruleGroup->isArchived()) {
            throw new BadStateException('ruleGroup', 'Only archived rule groups can be restored.');
        }

        $archivedRuleGroup = $this->layoutResolverHandler->loadRuleGroup($ruleGroup->getId(), Value::STATUS_ARCHIVED);

        $draftRuleGroup = null;

        try {
            $draftRuleGroup = $this->layoutResolverHandler->loadRuleGroup($ruleGroup->getId(), Value::STATUS_DRAFT);
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $draftRuleGroup = $this->transaction(
            function () use ($draftRuleGroup, $archivedRuleGroup): PersistenceRuleGroup {
                if ($draftRuleGroup instanceof PersistenceRuleGroup) {
                    $this->layoutResolverHandler->deleteRuleGroup($draftRuleGroup->id, $draftRuleGroup->status);
                }

                return $this->layoutResolverHandler->createRuleGroupStatus($archivedRuleGroup, Value::STATUS_DRAFT);
            },
        );

        return $this->mapper->mapRuleGroup($draftRuleGroup);
    }

    public function deleteRuleGroup(RuleGroup $ruleGroup): void
    {
        $persistenceRuleGroup = $this->layoutResolverHandler->loadRuleGroup($ruleGroup->getId(), $ruleGroup->getStatus());

        $this->transaction(
            function () use ($persistenceRuleGroup): void {
                $this->layoutResolverHandler->deleteRuleGroup(
                    $persistenceRuleGroup->id,
                );
            },
        );
    }

    public function enableRule(Rule $rule): Rule
    {
        if (!$rule->isPublished()) {
            throw new BadStateException('rule', 'Only published rules can be enabled.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->getId(), Value::STATUS_PUBLISHED);

        if ($persistenceRule->enabled) {
            throw new BadStateException('rule', 'Rule is already enabled.');
        }

        $updatedRule = $this->transaction(
            fn (): PersistenceRule => $this->layoutResolverHandler->updateRuleMetadata(
                $persistenceRule,
                RuleMetadataUpdateStruct::fromArray(
                    [
                        'enabled' => true,
                    ],
                ),
            ),
        );

        return $this->mapper->mapRule($updatedRule);
    }

    public function disableRule(Rule $rule): Rule
    {
        if (!$rule->isPublished()) {
            throw new BadStateException('rule', 'Only published rules can be disabled.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->getId(), Value::STATUS_PUBLISHED);

        if (!$persistenceRule->enabled) {
            throw new BadStateException('rule', 'Rule is already disabled.');
        }

        $updatedRule = $this->transaction(
            fn (): PersistenceRule => $this->layoutResolverHandler->updateRuleMetadata(
                $persistenceRule,
                RuleMetadataUpdateStruct::fromArray(
                    [
                        'enabled' => false,
                    ],
                ),
            ),
        );

        return $this->mapper->mapRule($updatedRule);
    }

    public function enableRuleGroup(RuleGroup $ruleGroup): RuleGroup
    {
        if (!$ruleGroup->isPublished()) {
            throw new BadStateException('ruleGroup', 'Only published rule groups can be enabled.');
        }

        $persistenceRuleGroup = $this->layoutResolverHandler->loadRuleGroup($ruleGroup->getId(), Value::STATUS_PUBLISHED);

        if ($persistenceRuleGroup->enabled) {
            throw new BadStateException('ruleGroup', 'Rule group is already enabled.');
        }

        $updatedRuleGroup = $this->transaction(
            fn (): PersistenceRuleGroup => $this->layoutResolverHandler->updateRuleGroupMetadata(
                $persistenceRuleGroup,
                RuleGroupMetadataUpdateStruct::fromArray(
                    [
                        'enabled' => true,
                    ],
                ),
            ),
        );

        return $this->mapper->mapRuleGroup($updatedRuleGroup);
    }

    public function disableRuleGroup(RuleGroup $ruleGroup): RuleGroup
    {
        if (!$ruleGroup->isPublished()) {
            throw new BadStateException('ruleGroup', 'Only published rule groups can be disabled.');
        }

        $persistenceRuleGroup = $this->layoutResolverHandler->loadRuleGroup($ruleGroup->getId(), Value::STATUS_PUBLISHED);

        if (!$persistenceRuleGroup->enabled) {
            throw new BadStateException('ruleGroup', 'Rule group is already disabled.');
        }

        $updatedRuleGroup = $this->transaction(
            fn (): PersistenceRuleGroup => $this->layoutResolverHandler->updateRuleGroupMetadata(
                $persistenceRuleGroup,
                RuleGroupMetadataUpdateStruct::fromArray(
                    [
                        'enabled' => false,
                    ],
                ),
            ),
        );

        return $this->mapper->mapRuleGroup($updatedRuleGroup);
    }

    public function addTarget(Rule $rule, APITargetCreateStruct $targetCreateStruct): Target
    {
        if (!$rule->isDraft()) {
            throw new BadStateException('rule', 'Targets can be added only to draft rules.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->getId(), Value::STATUS_DRAFT);
        $ruleTargets = $this->layoutResolverHandler->loadRuleTargets($persistenceRule);

        if (count($ruleTargets) > 0 && $ruleTargets[0]->type !== $targetCreateStruct->type) {
            throw new BadStateException(
                'rule',
                sprintf(
                    'Rule with UUID "%s" only accepts targets with "%s" target type.',
                    $rule->getId()->toString(),
                    $ruleTargets[0]->type,
                ),
            );
        }

        $this->validator->validateTargetCreateStruct($targetCreateStruct);

        $createdTarget = $this->transaction(
            fn (): PersistenceTarget => $this->layoutResolverHandler->addTarget(
                $persistenceRule,
                TargetCreateStruct::fromArray(
                    [
                        'type' => $targetCreateStruct->type,
                        'value' => $targetCreateStruct->value,
                    ],
                ),
            ),
        );

        return $this->mapper->mapTarget($createdTarget);
    }

    public function updateTarget(Target $target, APITargetUpdateStruct $targetUpdateStruct): Target
    {
        if (!$target->isDraft()) {
            throw new BadStateException('target', 'Only draft targets can be updated.');
        }

        $persistenceTarget = $this->layoutResolverHandler->loadTarget($target->getId(), Value::STATUS_DRAFT);

        $this->validator->validateTargetUpdateStruct($target, $targetUpdateStruct);

        $updatedTarget = $this->transaction(
            fn (): PersistenceTarget => $this->layoutResolverHandler->updateTarget(
                $persistenceTarget,
                TargetUpdateStruct::fromArray(
                    [
                        'value' => $targetUpdateStruct->value,
                    ],
                ),
            ),
        );

        return $this->mapper->mapTarget($updatedTarget);
    }

    public function deleteTarget(Target $target): void
    {
        if (!$target->isDraft()) {
            throw new BadStateException('target', 'Only draft targets can be deleted.');
        }

        $persistenceTarget = $this->layoutResolverHandler->loadTarget($target->getId(), Value::STATUS_DRAFT);

        $this->transaction(
            function () use ($persistenceTarget): void {
                $this->layoutResolverHandler->deleteTarget($persistenceTarget);
            },
        );
    }

    public function addCondition(Rule $rule, APIConditionCreateStruct $conditionCreateStruct): RuleCondition
    {
        return $this->addRuleCondition($rule, $conditionCreateStruct);
    }

    public function addRuleCondition(Rule $rule, APIConditionCreateStruct $conditionCreateStruct): RuleCondition
    {
        if (!$rule->isDraft()) {
            throw new BadStateException('rule', 'Conditions can be added only to draft rules.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->getId(), Value::STATUS_DRAFT);

        $this->validator->validateConditionCreateStruct($conditionCreateStruct);

        $createdCondition = $this->transaction(
            fn (): PersistenceRuleCondition => $this->layoutResolverHandler->addRuleCondition(
                $persistenceRule,
                ConditionCreateStruct::fromArray(
                    [
                        'type' => $conditionCreateStruct->type,
                        'value' => $conditionCreateStruct->value,
                    ],
                ),
            ),
        );

        return $this->mapper->mapRuleCondition($createdCondition);
    }

    public function addRuleGroupCondition(RuleGroup $ruleGroup, APIConditionCreateStruct $conditionCreateStruct): RuleGroupCondition
    {
        if (!$ruleGroup->isDraft()) {
            throw new BadStateException('ruleGroup', 'Conditions can be added only to draft rule groups.');
        }

        $persistenceRuleGroup = $this->layoutResolverHandler->loadRuleGroup($ruleGroup->getId(), Value::STATUS_DRAFT);

        $this->validator->validateConditionCreateStruct($conditionCreateStruct);

        $createdCondition = $this->transaction(
            fn (): PersistenceRuleGroupCondition => $this->layoutResolverHandler->addRuleGroupCondition(
                $persistenceRuleGroup,
                ConditionCreateStruct::fromArray(
                    [
                        'type' => $conditionCreateStruct->type,
                        'value' => $conditionCreateStruct->value,
                    ],
                ),
            ),
        );

        return $this->mapper->mapRuleGroupCondition($createdCondition);
    }

    public function updateCondition(RuleCondition $condition, APIConditionUpdateStruct $conditionUpdateStruct): RuleCondition
    {
        return $this->updateRuleCondition($condition, $conditionUpdateStruct);
    }

    public function updateRuleCondition(RuleCondition $condition, APIConditionUpdateStruct $conditionUpdateStruct): RuleCondition
    {
        if (!$condition->isDraft()) {
            throw new BadStateException('condition', 'Only draft conditions can be updated.');
        }

        $persistenceCondition = $this->layoutResolverHandler->loadRuleCondition($condition->getId(), Value::STATUS_DRAFT);

        $this->validator->validateConditionUpdateStruct($condition, $conditionUpdateStruct);

        $updatedCondition = $this->transaction(
            fn (): PersistenceCondition => $this->layoutResolverHandler->updateCondition(
                $persistenceCondition,
                ConditionUpdateStruct::fromArray(
                    [
                        'value' => $conditionUpdateStruct->value,
                    ],
                ),
            ),
        );

        return $this->mapper->mapRuleCondition($updatedCondition);
    }

    public function updateRuleGroupCondition(RuleGroupCondition $condition, APIConditionUpdateStruct $conditionUpdateStruct): RuleGroupCondition
    {
        if (!$condition->isDraft()) {
            throw new BadStateException('condition', 'Only draft conditions can be updated.');
        }

        $persistenceCondition = $this->layoutResolverHandler->loadRuleGroupCondition($condition->getId(), Value::STATUS_DRAFT);

        $this->validator->validateConditionUpdateStruct($condition, $conditionUpdateStruct);

        $updatedCondition = $this->transaction(
            fn (): PersistenceCondition => $this->layoutResolverHandler->updateCondition(
                $persistenceCondition,
                ConditionUpdateStruct::fromArray(
                    [
                        'value' => $conditionUpdateStruct->value,
                    ],
                ),
            ),
        );

        return $this->mapper->mapRuleGroupCondition($updatedCondition);
    }

    public function deleteCondition(Condition $condition): void
    {
        if (!$condition->isDraft()) {
            throw new BadStateException('condition', 'Only draft conditions can be deleted.');
        }

        $persistenceCondition = $condition instanceof RuleCondition ?
            $this->layoutResolverHandler->loadRuleCondition($condition->getId(), Value::STATUS_DRAFT) :
            $this->layoutResolverHandler->loadRuleGroupCondition($condition->getId(), Value::STATUS_DRAFT);

        $this->transaction(
            function () use ($persistenceCondition): void {
                $this->layoutResolverHandler->deleteCondition($persistenceCondition);
            },
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

    public function newRuleGroupCreateStruct(string $name): APIRuleGroupCreateStruct
    {
        return $this->structBuilder->newRuleGroupCreateStruct($name);
    }

    public function newRuleGroupUpdateStruct(): APIRuleGroupUpdateStruct
    {
        return $this->structBuilder->newRuleGroupUpdateStruct();
    }

    public function newRuleGroupMetadataUpdateStruct(): APIRuleGroupMetadataUpdateStruct
    {
        return $this->structBuilder->newRuleGroupMetadataUpdateStruct();
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
