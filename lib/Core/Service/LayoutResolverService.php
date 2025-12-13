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
use Netgen\Layouts\API\Values\Status;
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
use Netgen\Layouts\Persistence\Values\Status as PersistenceStatus;
use Symfony\Component\Uid\Uuid;

use function array_map;
use function count;
use function sprintf;

final class LayoutResolverService implements APILayoutResolverService
{
    use TransactionTrait;

    public function __construct(
        TransactionHandlerInterface $transactionHandler,
        private LayoutResolverValidator $validator,
        private LayoutResolverMapper $mapper,
        private LayoutResolverStructBuilder $structBuilder,
        private LayoutResolverHandlerInterface $layoutResolverHandler,
        private LayoutHandlerInterface $layoutHandler,
    ) {
        $this->transactionHandler = $transactionHandler;
    }

    public function loadRule(Uuid $ruleId): Rule
    {
        return $this->mapper->mapRule(
            $this->layoutResolverHandler->loadRule(
                $ruleId,
                PersistenceStatus::Published,
            ),
        );
    }

    public function loadRuleDraft(Uuid $ruleId): Rule
    {
        return $this->mapper->mapRule(
            $this->layoutResolverHandler->loadRule(
                $ruleId,
                PersistenceStatus::Draft,
            ),
        );
    }

    public function loadRuleArchive(Uuid $ruleId): Rule
    {
        return $this->mapper->mapRule(
            $this->layoutResolverHandler->loadRule(
                $ruleId,
                PersistenceStatus::Archived,
            ),
        );
    }

    public function loadRuleGroup(Uuid $ruleGroupId): RuleGroup
    {
        return $this->mapper->mapRuleGroup(
            $this->layoutResolverHandler->loadRuleGroup(
                $ruleGroupId,
                PersistenceStatus::Published,
            ),
        );
    }

    public function loadRuleGroupDraft(Uuid $ruleGroupId): RuleGroup
    {
        return $this->mapper->mapRuleGroup(
            $this->layoutResolverHandler->loadRuleGroup(
                $ruleGroupId,
                PersistenceStatus::Draft,
            ),
        );
    }

    public function loadRuleGroupArchive(Uuid $ruleGroupId): RuleGroup
    {
        return $this->mapper->mapRuleGroup(
            $this->layoutResolverHandler->loadRuleGroup(
                $ruleGroupId,
                PersistenceStatus::Archived,
            ),
        );
    }

    public function loadRulesForLayout(Layout $layout, int $offset = 0, ?int $limit = null, bool $ascending = false): RuleList
    {
        if (!$layout->isPublished) {
            throw new BadStateException('layout', 'Only published layouts can be used in rules.');
        }

        $persistenceLayout = $this->layoutHandler->loadLayout(
            $layout->id,
            PersistenceStatus::Published,
        );

        $persistenceRules = $this->layoutResolverHandler->loadRulesForLayout(
            $persistenceLayout,
            $offset,
            $limit,
            $ascending,
        );

        return RuleList::fromArray(
            array_map(
                $this->mapper->mapRule(...),
                $persistenceRules,
            ),
        );
    }

    public function getRuleCountForLayout(Layout $layout): int
    {
        if (!$layout->isPublished) {
            throw new BadStateException('layout', 'Only published layouts can be used in rules.');
        }

        $persistenceLayout = $this->layoutHandler->loadLayout(
            $layout->id,
            PersistenceStatus::Published,
        );

        return $this->layoutResolverHandler->getRuleCountForLayout($persistenceLayout);
    }

    public function loadRulesFromGroup(RuleGroup $ruleGroup, int $offset = 0, ?int $limit = null, bool $ascending = false): RuleList
    {
        if (!$ruleGroup->isPublished) {
            throw new BadStateException('ruleGroup', 'Rules can be loaded only from published rule groups.');
        }

        $persistenceGroup = $this->layoutResolverHandler->loadRuleGroup(
            $ruleGroup->id,
            PersistenceStatus::Published,
        );

        $persistenceRules = $this->layoutResolverHandler->loadRulesFromGroup(
            $persistenceGroup,
            $offset,
            $limit,
            $ascending,
        );

        return RuleList::fromArray(
            array_map(
                $this->mapper->mapRule(...),
                $persistenceRules,
            ),
        );
    }

    public function getRuleCountFromGroup(RuleGroup $ruleGroup): int
    {
        if (!$ruleGroup->isPublished) {
            throw new BadStateException('ruleGroup', 'Rule count can be fetched only for published rule groups.');
        }

        $persistenceGroup = $this->layoutResolverHandler->loadRuleGroup(
            $ruleGroup->id,
            PersistenceStatus::Published,
        );

        return $this->layoutResolverHandler->getRuleCountFromGroup($persistenceGroup);
    }

    public function loadRuleGroups(RuleGroup $parentGroup, int $offset = 0, ?int $limit = null, bool $ascending = false): RuleGroupList
    {
        if (!$parentGroup->isPublished) {
            throw new BadStateException('parentGroup', 'Rule groups can be loaded only from published parent groups.');
        }

        $persistenceParentGroup = $this->layoutResolverHandler->loadRuleGroup(
            $parentGroup->id,
            PersistenceStatus::Published,
        );

        $persistenceRuleGroups = $this->layoutResolverHandler->loadRuleGroups(
            $persistenceParentGroup,
            $offset,
            $limit,
            $ascending,
        );

        return new RuleGroupList(
            array_map(
                $this->mapper->mapRuleGroup(...),
                $persistenceRuleGroups,
            ),
        );
    }

    public function getRuleGroupCount(RuleGroup $parentGroup): int
    {
        if (!$parentGroup->isPublished) {
            throw new BadStateException('parentGroup', 'Rule group count can be fetched only for published parent groups.');
        }

        $persistenceParentGroup = $this->layoutResolverHandler->loadRuleGroup(
            $parentGroup->id,
            PersistenceStatus::Published,
        );

        return $this->layoutResolverHandler->getRuleGroupCount($persistenceParentGroup);
    }

    public function matchRules(RuleGroup $ruleGroup, string $targetType, mixed $targetValue): RuleList
    {
        $persistenceGroup = $this->layoutResolverHandler->loadRuleGroup(
            $ruleGroup->id,
            PersistenceStatus::from($ruleGroup->status->value),
        );

        return RuleList::fromArray(
            array_map(
                $this->mapper->mapRule(...),
                $this->layoutResolverHandler->matchRules($persistenceGroup, $targetType, $targetValue),
            ),
        );
    }

    public function loadTarget(Uuid $targetId): Target
    {
        return $this->mapper->mapTarget(
            $this->layoutResolverHandler->loadTarget(
                $targetId,
                PersistenceStatus::Published,
            ),
        );
    }

    public function loadTargetDraft(Uuid $targetId): Target
    {
        return $this->mapper->mapTarget(
            $this->layoutResolverHandler->loadTarget(
                $targetId,
                PersistenceStatus::Draft,
            ),
        );
    }

    public function loadRuleCondition(Uuid $conditionId): RuleCondition
    {
        return $this->mapper->mapRuleCondition(
            $this->layoutResolverHandler->loadRuleCondition(
                $conditionId,
                PersistenceStatus::Published,
            ),
        );
    }

    public function loadRuleConditionDraft(Uuid $conditionId): RuleCondition
    {
        return $this->mapper->mapRuleCondition(
            $this->layoutResolverHandler->loadRuleCondition(
                $conditionId,
                PersistenceStatus::Draft,
            ),
        );
    }

    public function loadRuleGroupCondition(Uuid $conditionId): RuleGroupCondition
    {
        return $this->mapper->mapRuleGroupCondition(
            $this->layoutResolverHandler->loadRuleGroupCondition(
                $conditionId,
                PersistenceStatus::Published,
            ),
        );
    }

    public function loadRuleGroupConditionDraft(Uuid $conditionId): RuleGroupCondition
    {
        return $this->mapper->mapRuleGroupCondition(
            $this->layoutResolverHandler->loadRuleGroupCondition(
                $conditionId,
                PersistenceStatus::Draft,
            ),
        );
    }

    public function ruleExists(Uuid $ruleId, ?Status $status = null): bool
    {
        return $this->layoutResolverHandler->ruleExists($ruleId, PersistenceStatus::tryFrom($status->value ?? -1));
    }

    public function createRule(APIRuleCreateStruct $ruleCreateStruct, RuleGroup $targetGroup): Rule
    {
        if (!$targetGroup->isPublished) {
            throw new BadStateException('targetGroup', 'Rules can be created only in published groups.');
        }

        $createdRule = $this->transaction(
            fn (): PersistenceRule => $this->layoutResolverHandler->createRule(
                RuleCreateStruct::fromArray(
                    [
                        'uuid' => $ruleCreateStruct->uuid instanceof Uuid ?
                            $ruleCreateStruct->uuid->toString() :
                            $ruleCreateStruct->uuid,
                        'layoutId' => $ruleCreateStruct->layoutId instanceof Uuid ?
                            $ruleCreateStruct->layoutId->toString() :
                            $ruleCreateStruct->layoutId,
                        'priority' => $ruleCreateStruct->priority,
                        'isEnabled' => $ruleCreateStruct->isEnabled,
                        'description' => $ruleCreateStruct->description,
                        'status' => PersistenceStatus::Draft,
                    ],
                ),
                $this->layoutResolverHandler->loadRuleGroup($targetGroup->id, PersistenceStatus::Published),
            ),
        );

        return $this->mapper->mapRule($createdRule);
    }

    public function updateRule(Rule $rule, APIRuleUpdateStruct $ruleUpdateStruct): Rule
    {
        if (!$rule->isDraft) {
            throw new BadStateException('rule', 'Only draft rules can be updated.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->id, PersistenceStatus::Draft);

        $this->validator->validateRuleUpdateStruct($ruleUpdateStruct);

        $updatedRule = $this->transaction(
            fn (): PersistenceRule => $this->layoutResolverHandler->updateRule(
                $persistenceRule,
                RuleUpdateStruct::fromArray(
                    [
                        'layoutId' => $ruleUpdateStruct->layoutId instanceof Uuid ?
                            $ruleUpdateStruct->layoutId->toString() :
                            $ruleUpdateStruct->layoutId,
                        'description' => $ruleUpdateStruct->description,
                    ],
                ),
            ),
        );

        return $this->mapper->mapRule($updatedRule);
    }

    public function updateRuleMetadata(Rule $rule, APIRuleMetadataUpdateStruct $ruleUpdateStruct): Rule
    {
        if (!$rule->isPublished) {
            throw new BadStateException('rule', 'Metadata can be updated only for published rules.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->id, PersistenceStatus::Published);

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
        if (!$rule->isPublished) {
            throw new BadStateException('rule', 'Only published rules can be copied.');
        }

        if (!$targetGroup->isPublished) {
            throw new BadStateException('targetGroup', 'Rules can be copied only to published groups.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->id, PersistenceStatus::Published);
        $persistenceGroup = $this->layoutResolverHandler->loadRuleGroup($targetGroup->id, PersistenceStatus::Published);

        $copiedRule = $this->transaction(
            fn (): PersistenceRule => $this->layoutResolverHandler->copyRule($persistenceRule, $persistenceGroup),
        );

        return $this->mapper->mapRule($copiedRule);
    }

    public function moveRule(Rule $rule, RuleGroup $targetGroup, ?int $newPriority = null): Rule
    {
        if (!$rule->isPublished) {
            throw new BadStateException('rule', 'Only published rules can be moved.');
        }

        if (!$targetGroup->isPublished) {
            throw new BadStateException('targetGroup', 'Rules can be moved only to published groups.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->id, PersistenceStatus::Published);
        $persistenceTargetGroup = $this->layoutResolverHandler->loadRuleGroup($targetGroup->id, PersistenceStatus::Published);

        $movedRule = $this->transaction(
            fn (): PersistenceRule => $this->layoutResolverHandler->moveRule(
                $persistenceRule,
                $persistenceTargetGroup,
                $newPriority,
            ),
        );

        return $this->mapper->mapRule($movedRule);
    }

    public function createRuleDraft(Rule $rule, bool $discardExisting = false): Rule
    {
        if (!$rule->isPublished) {
            throw new BadStateException('rule', 'Drafts can only be created from published rules.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->id, PersistenceStatus::Published);

        if (!$discardExisting && $this->layoutResolverHandler->ruleExists($persistenceRule->id, PersistenceStatus::Draft)) {
            throw new BadStateException('rule', 'The provided rule already has a draft.');
        }

        $ruleDraft = $this->transaction(
            function () use ($persistenceRule): PersistenceRule {
                $this->layoutResolverHandler->deleteRule($persistenceRule->id, PersistenceStatus::Draft);

                return $this->layoutResolverHandler->createRuleStatus($persistenceRule, PersistenceStatus::Draft);
            },
        );

        return $this->mapper->mapRule($ruleDraft);
    }

    public function discardRuleDraft(Rule $rule): void
    {
        if (!$rule->isDraft) {
            throw new BadStateException('rule', 'Only draft rules can be discarded.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->id, PersistenceStatus::Draft);

        $this->transaction(
            function () use ($persistenceRule): void {
                $this->layoutResolverHandler->deleteRule(
                    $persistenceRule->id,
                    PersistenceStatus::Draft,
                );
            },
        );
    }

    public function publishRule(Rule $rule): Rule
    {
        if (!$rule->isDraft) {
            throw new BadStateException('rule', 'Only draft rules can be published.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->id, PersistenceStatus::Draft);

        $publishedRule = $this->transaction(
            function () use ($persistenceRule): PersistenceRule {
                $this->layoutResolverHandler->deleteRule($persistenceRule->id, PersistenceStatus::Archived);

                if ($this->layoutResolverHandler->ruleExists($persistenceRule->id, PersistenceStatus::Published)) {
                    $this->layoutResolverHandler->createRuleStatus(
                        $this->layoutResolverHandler->loadRule(
                            $persistenceRule->id,
                            PersistenceStatus::Published,
                        ),
                        PersistenceStatus::Archived,
                    );

                    $this->layoutResolverHandler->deleteRule($persistenceRule->id, PersistenceStatus::Published);
                }

                $publishedRule = $this->layoutResolverHandler->createRuleStatus($persistenceRule, PersistenceStatus::Published);
                $this->layoutResolverHandler->deleteRule($persistenceRule->id, PersistenceStatus::Draft);

                return $publishedRule;
            },
        );

        return $this->mapper->mapRule($publishedRule);
    }

    public function restoreRuleFromArchive(Rule $rule): Rule
    {
        if (!$rule->isArchived) {
            throw new BadStateException('rule', 'Only archived rules can be restored.');
        }

        $archivedRule = $this->layoutResolverHandler->loadRule($rule->id, PersistenceStatus::Archived);

        $draftRule = null;

        try {
            $draftRule = $this->layoutResolverHandler->loadRule($rule->id, PersistenceStatus::Draft);
        } catch (NotFoundException) {
            // Do nothing
        }

        $draftRule = $this->transaction(
            function () use ($draftRule, $archivedRule): PersistenceRule {
                if ($draftRule instanceof PersistenceRule) {
                    $this->layoutResolverHandler->deleteRule($draftRule->id, $draftRule->status);
                }

                return $this->layoutResolverHandler->createRuleStatus($archivedRule, PersistenceStatus::Draft);
            },
        );

        return $this->mapper->mapRule($draftRule);
    }

    public function deleteRule(Rule $rule): void
    {
        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->id, PersistenceStatus::from($rule->status->value));

        $this->transaction(
            function () use ($persistenceRule): void {
                $this->layoutResolverHandler->deleteRule(
                    $persistenceRule->id,
                );
            },
        );
    }

    public function ruleGroupExists(Uuid $ruleGroupId, ?Status $status = null): bool
    {
        return $this->layoutResolverHandler->ruleGroupExists($ruleGroupId, PersistenceStatus::tryFrom($status->value ?? -1));
    }

    public function createRuleGroup(APIRuleGroupCreateStruct $ruleGroupCreateStruct, ?RuleGroup $parentGroup = null): RuleGroup
    {
        $this->validator->validateRuleGroupCreateStruct($ruleGroupCreateStruct);

        if ($parentGroup !== null && !$parentGroup->isPublished) {
            throw new BadStateException('parentGroup', 'Rule groups can be created only in published groups.');
        }

        $createdRuleGroup = $this->transaction(
            fn (): PersistenceRuleGroup => $this->layoutResolverHandler->createRuleGroup(
                RuleGroupCreateStruct::fromArray(
                    [
                        'uuid' => $ruleGroupCreateStruct->uuid instanceof Uuid ?
                            $ruleGroupCreateStruct->uuid->toString() :
                            $ruleGroupCreateStruct->uuid,
                        'name' => $ruleGroupCreateStruct->name,
                        'description' => $ruleGroupCreateStruct->description,
                        'priority' => $ruleGroupCreateStruct->priority,
                        'isEnabled' => $ruleGroupCreateStruct->isEnabled,
                        'status' => PersistenceStatus::Draft,
                    ],
                ),
                $parentGroup !== null ?
                    $this->layoutResolverHandler->loadRuleGroup($parentGroup->id, PersistenceStatus::Published) :
                    null,
            ),
        );

        return $this->mapper->mapRuleGroup($createdRuleGroup);
    }

    public function updateRuleGroup(RuleGroup $ruleGroup, APIRuleGroupUpdateStruct $ruleGroupUpdateStruct): RuleGroup
    {
        if (!$ruleGroup->isDraft) {
            throw new BadStateException('ruleGroup', 'Only draft rule groups can be updated.');
        }

        $persistenceRuleGroup = $this->layoutResolverHandler->loadRuleGroup($ruleGroup->id, PersistenceStatus::Draft);

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
        if (!$ruleGroup->isPublished) {
            throw new BadStateException('ruleGroup', 'Metadata can be updated only for published rule groups.');
        }

        $persistenceRuleGroup = $this->layoutResolverHandler->loadRuleGroup($ruleGroup->id, PersistenceStatus::Published);

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
        if (!$ruleGroup->isPublished) {
            throw new BadStateException('ruleGroup', 'Only published rule groups can be copied.');
        }

        if (!$targetGroup->isPublished) {
            throw new BadStateException('targetGroup', 'Rule groups can be copied only to published groups.');
        }

        $persistenceRuleGroup = $this->layoutResolverHandler->loadRuleGroup($ruleGroup->id, PersistenceStatus::Published);
        $persistenceTargetGroup = $this->layoutResolverHandler->loadRuleGroup($targetGroup->id, PersistenceStatus::Published);

        $copiedRuleGroup = $this->transaction(
            fn (): PersistenceRuleGroup => $this->layoutResolverHandler->copyRuleGroup($persistenceRuleGroup, $persistenceTargetGroup, $copyChildren),
        );

        return $this->mapper->mapRuleGroup($copiedRuleGroup);
    }

    public function moveRuleGroup(RuleGroup $ruleGroup, RuleGroup $targetGroup, ?int $newPriority = null): RuleGroup
    {
        if (!$ruleGroup->isPublished) {
            throw new BadStateException('ruleGroup', 'Only published rule groups can be moved.');
        }

        if (!$targetGroup->isPublished) {
            throw new BadStateException('targetGroup', 'Rule groups can be moved only to published groups.');
        }

        $persistenceRuleGroup = $this->layoutResolverHandler->loadRuleGroup($ruleGroup->id, PersistenceStatus::Published);
        $persistenceTargetGroup = $this->layoutResolverHandler->loadRuleGroup($targetGroup->id, PersistenceStatus::Published);

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
        if (!$ruleGroup->isPublished) {
            throw new BadStateException('ruleGroup', 'Drafts can only be created from published rule groups.');
        }

        $persistenceRuleGroup = $this->layoutResolverHandler->loadRuleGroup($ruleGroup->id, PersistenceStatus::Published);

        if (!$discardExisting && $this->layoutResolverHandler->ruleGroupExists($persistenceRuleGroup->id, PersistenceStatus::Draft)) {
            throw new BadStateException('ruleGroup', 'The provided rule group already has a draft.');
        }

        $ruleGroupDraft = $this->transaction(
            function () use ($persistenceRuleGroup): PersistenceRuleGroup {
                $this->layoutResolverHandler->deleteRuleGroup($persistenceRuleGroup->id, PersistenceStatus::Draft);

                return $this->layoutResolverHandler->createRuleGroupStatus($persistenceRuleGroup, PersistenceStatus::Draft);
            },
        );

        return $this->mapper->mapRuleGroup($ruleGroupDraft);
    }

    public function discardRuleGroupDraft(RuleGroup $ruleGroup): void
    {
        if (!$ruleGroup->isDraft) {
            throw new BadStateException('ruleGroup', 'Only draft rule groups can be discarded.');
        }

        $persistenceRuleGroup = $this->layoutResolverHandler->loadRuleGroup($ruleGroup->id, PersistenceStatus::Draft);

        $this->transaction(
            function () use ($persistenceRuleGroup): void {
                $this->layoutResolverHandler->deleteRuleGroup(
                    $persistenceRuleGroup->id,
                    PersistenceStatus::Draft,
                );
            },
        );
    }

    public function publishRuleGroup(RuleGroup $ruleGroup): RuleGroup
    {
        if (!$ruleGroup->isDraft) {
            throw new BadStateException('ruleGroup', 'Only draft rule groups can be published.');
        }

        $persistenceRuleGroup = $this->layoutResolverHandler->loadRuleGroup($ruleGroup->id, PersistenceStatus::Draft);

        $publishedRuleGroup = $this->transaction(
            function () use ($persistenceRuleGroup): PersistenceRuleGroup {
                $this->layoutResolverHandler->deleteRuleGroup($persistenceRuleGroup->id, PersistenceStatus::Archived);

                if ($this->layoutResolverHandler->ruleGroupExists($persistenceRuleGroup->id, PersistenceStatus::Published)) {
                    $this->layoutResolverHandler->createRuleGroupStatus(
                        $this->layoutResolverHandler->loadRuleGroup(
                            $persistenceRuleGroup->id,
                            PersistenceStatus::Published,
                        ),
                        PersistenceStatus::Archived,
                    );

                    $this->layoutResolverHandler->deleteRuleGroup($persistenceRuleGroup->id, PersistenceStatus::Published);
                }

                $publishedRuleGroup = $this->layoutResolverHandler->createRuleGroupStatus($persistenceRuleGroup, PersistenceStatus::Published);
                $this->layoutResolverHandler->deleteRuleGroup($persistenceRuleGroup->id, PersistenceStatus::Draft);

                return $publishedRuleGroup;
            },
        );

        return $this->mapper->mapRuleGroup($publishedRuleGroup);
    }

    public function restoreRuleGroupFromArchive(RuleGroup $ruleGroup): RuleGroup
    {
        if (!$ruleGroup->isArchived) {
            throw new BadStateException('ruleGroup', 'Only archived rule groups can be restored.');
        }

        $archivedRuleGroup = $this->layoutResolverHandler->loadRuleGroup($ruleGroup->id, PersistenceStatus::Archived);

        $draftRuleGroup = null;

        try {
            $draftRuleGroup = $this->layoutResolverHandler->loadRuleGroup($ruleGroup->id, PersistenceStatus::Draft);
        } catch (NotFoundException) {
            // Do nothing
        }

        $draftRuleGroup = $this->transaction(
            function () use ($draftRuleGroup, $archivedRuleGroup): PersistenceRuleGroup {
                if ($draftRuleGroup instanceof PersistenceRuleGroup) {
                    $this->layoutResolverHandler->deleteRuleGroup($draftRuleGroup->id, $draftRuleGroup->status);
                }

                return $this->layoutResolverHandler->createRuleGroupStatus($archivedRuleGroup, PersistenceStatus::Draft);
            },
        );

        return $this->mapper->mapRuleGroup($draftRuleGroup);
    }

    public function deleteRuleGroup(RuleGroup $ruleGroup): void
    {
        $persistenceRuleGroup = $this->layoutResolverHandler->loadRuleGroup($ruleGroup->id, PersistenceStatus::from($ruleGroup->status->value));

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
        if (!$rule->isPublished) {
            throw new BadStateException('rule', 'Only published rules can be enabled.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->id, PersistenceStatus::Published);

        if ($persistenceRule->isEnabled) {
            throw new BadStateException('rule', 'Rule is already enabled.');
        }

        $updatedRule = $this->transaction(
            fn (): PersistenceRule => $this->layoutResolverHandler->updateRuleMetadata(
                $persistenceRule,
                RuleMetadataUpdateStruct::fromArray(
                    [
                        'isEnabled' => true,
                    ],
                ),
            ),
        );

        return $this->mapper->mapRule($updatedRule);
    }

    public function disableRule(Rule $rule): Rule
    {
        if (!$rule->isPublished) {
            throw new BadStateException('rule', 'Only published rules can be disabled.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->id, PersistenceStatus::Published);

        if (!$persistenceRule->isEnabled) {
            throw new BadStateException('rule', 'Rule is already disabled.');
        }

        $updatedRule = $this->transaction(
            fn (): PersistenceRule => $this->layoutResolverHandler->updateRuleMetadata(
                $persistenceRule,
                RuleMetadataUpdateStruct::fromArray(
                    [
                        'isEnabled' => false,
                    ],
                ),
            ),
        );

        return $this->mapper->mapRule($updatedRule);
    }

    public function enableRuleGroup(RuleGroup $ruleGroup): RuleGroup
    {
        if (!$ruleGroup->isPublished) {
            throw new BadStateException('ruleGroup', 'Only published rule groups can be enabled.');
        }

        $persistenceRuleGroup = $this->layoutResolverHandler->loadRuleGroup($ruleGroup->id, PersistenceStatus::Published);

        if ($persistenceRuleGroup->isEnabled) {
            throw new BadStateException('ruleGroup', 'Rule group is already enabled.');
        }

        $updatedRuleGroup = $this->transaction(
            fn (): PersistenceRuleGroup => $this->layoutResolverHandler->updateRuleGroupMetadata(
                $persistenceRuleGroup,
                RuleGroupMetadataUpdateStruct::fromArray(
                    [
                        'isEnabled' => true,
                    ],
                ),
            ),
        );

        return $this->mapper->mapRuleGroup($updatedRuleGroup);
    }

    public function disableRuleGroup(RuleGroup $ruleGroup): RuleGroup
    {
        if (!$ruleGroup->isPublished) {
            throw new BadStateException('ruleGroup', 'Only published rule groups can be disabled.');
        }

        $persistenceRuleGroup = $this->layoutResolverHandler->loadRuleGroup($ruleGroup->id, PersistenceStatus::Published);

        if (!$persistenceRuleGroup->isEnabled) {
            throw new BadStateException('ruleGroup', 'Rule group is already disabled.');
        }

        $updatedRuleGroup = $this->transaction(
            fn (): PersistenceRuleGroup => $this->layoutResolverHandler->updateRuleGroupMetadata(
                $persistenceRuleGroup,
                RuleGroupMetadataUpdateStruct::fromArray(
                    [
                        'isEnabled' => false,
                    ],
                ),
            ),
        );

        return $this->mapper->mapRuleGroup($updatedRuleGroup);
    }

    public function addTarget(Rule $rule, APITargetCreateStruct $targetCreateStruct): Target
    {
        if (!$rule->isDraft) {
            throw new BadStateException('rule', 'Targets can be added only to draft rules.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->id, PersistenceStatus::Draft);
        $ruleTargets = $this->layoutResolverHandler->loadRuleTargets($persistenceRule);

        if (count($ruleTargets) > 0 && $ruleTargets[0]->type !== $targetCreateStruct->type) {
            throw new BadStateException(
                'rule',
                sprintf(
                    'Rule with UUID "%s" only accepts targets with "%s" target type.',
                    $rule->id->toString(),
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
        if (!$target->isDraft) {
            throw new BadStateException('target', 'Only draft targets can be updated.');
        }

        $persistenceTarget = $this->layoutResolverHandler->loadTarget($target->id, PersistenceStatus::Draft);

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
        if (!$target->isDraft) {
            throw new BadStateException('target', 'Only draft targets can be deleted.');
        }

        $persistenceTarget = $this->layoutResolverHandler->loadTarget($target->id, PersistenceStatus::Draft);

        $this->transaction(
            function () use ($persistenceTarget): void {
                $this->layoutResolverHandler->deleteTarget($persistenceTarget);
            },
        );
    }

    public function addRuleCondition(Rule $rule, APIConditionCreateStruct $conditionCreateStruct): RuleCondition
    {
        if (!$rule->isDraft) {
            throw new BadStateException('rule', 'Conditions can be added only to draft rules.');
        }

        $persistenceRule = $this->layoutResolverHandler->loadRule($rule->id, PersistenceStatus::Draft);

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
        if (!$ruleGroup->isDraft) {
            throw new BadStateException('ruleGroup', 'Conditions can be added only to draft rule groups.');
        }

        $persistenceRuleGroup = $this->layoutResolverHandler->loadRuleGroup($ruleGroup->id, PersistenceStatus::Draft);

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

    public function updateRuleCondition(RuleCondition $condition, APIConditionUpdateStruct $conditionUpdateStruct): RuleCondition
    {
        if (!$condition->isDraft) {
            throw new BadStateException('condition', 'Only draft conditions can be updated.');
        }

        $persistenceCondition = $this->layoutResolverHandler->loadRuleCondition($condition->id, PersistenceStatus::Draft);

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
        if (!$condition->isDraft) {
            throw new BadStateException('condition', 'Only draft conditions can be updated.');
        }

        $persistenceCondition = $this->layoutResolverHandler->loadRuleGroupCondition($condition->id, PersistenceStatus::Draft);

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
        if (!$condition->isDraft) {
            throw new BadStateException('condition', 'Only draft conditions can be deleted.');
        }

        $persistenceCondition = $condition instanceof RuleCondition ?
            $this->layoutResolverHandler->loadRuleCondition($condition->id, PersistenceStatus::Draft) :
            $this->layoutResolverHandler->loadRuleGroupCondition($condition->id, PersistenceStatus::Draft);

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
