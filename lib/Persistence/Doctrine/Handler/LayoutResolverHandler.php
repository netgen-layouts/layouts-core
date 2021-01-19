<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\Handler;

use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Persistence\Doctrine\Mapper\LayoutResolverMapper;
use Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler;
use Netgen\Layouts\Persistence\Handler\LayoutHandlerInterface;
use Netgen\Layouts\Persistence\Handler\LayoutResolverHandlerInterface;
use Netgen\Layouts\Persistence\Values\Layout\Layout;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Condition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Rule;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleCreateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroupCreateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroupMetadataUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroupUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Target;
use Netgen\Layouts\Persistence\Values\LayoutResolver\TargetCreateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\TargetUpdateStruct;
use Netgen\Layouts\Persistence\Values\Value;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use function count;
use function is_bool;
use function is_int;
use function is_string;
use function str_starts_with;
use function trim;

final class LayoutResolverHandler implements LayoutResolverHandlerInterface
{
    /**
     * @var \Netgen\Layouts\Persistence\Handler\LayoutHandlerInterface
     */
    private $layoutHandler;

    /**
     * @var \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler
     */
    private $queryHandler;

    /**
     * @var \Netgen\Layouts\Persistence\Doctrine\Mapper\LayoutResolverMapper
     */
    private $mapper;

    public function __construct(
        LayoutHandlerInterface $layoutHandler,
        LayoutResolverQueryHandler $queryHandler,
        LayoutResolverMapper $mapper
    ) {
        $this->layoutHandler = $layoutHandler;
        $this->queryHandler = $queryHandler;
        $this->mapper = $mapper;
    }

    public function loadRule($ruleId, int $status): Rule
    {
        $ruleId = $ruleId instanceof UuidInterface ? $ruleId->toString() : $ruleId;
        $data = $this->queryHandler->loadRuleData($ruleId, $status);

        if (count($data) === 0) {
            throw new NotFoundException('rule', $ruleId);
        }

        return $this->mapper->mapRules($data)[0];
    }

    public function loadRuleGroup($ruleGroupId, int $status): RuleGroup
    {
        $ruleGroupId = $ruleGroupId instanceof UuidInterface ? $ruleGroupId->toString() : $ruleGroupId;
        $data = $this->queryHandler->loadRuleGroupData($ruleGroupId, $status);

        if (count($data) === 0) {
            throw new NotFoundException('rule group', $ruleGroupId);
        }

        return $this->mapper->mapRuleGroups($data)[0];
    }

    public function loadRules(int $status, ?Layout $layout = null, int $offset = 0, ?int $limit = null): array
    {
        $data = $this->queryHandler->loadRulesData($status, $layout, $offset, $limit);

        return $this->mapper->mapRules($data);
    }

    public function getRuleCount(?Layout $layout = null): int
    {
        return $this->queryHandler->getRuleCount(Value::STATUS_PUBLISHED, $layout);
    }

    public function loadRulesFromGroup(RuleGroup $ruleGroup, int $offset = 0, ?int $limit = null): array
    {
        $data = $this->queryHandler->loadRulesFromGroupData($ruleGroup, $offset, $limit);

        return $this->mapper->mapRules($data);
    }

    public function getRuleCountFromGroup(RuleGroup $ruleGroup): int
    {
        return $this->queryHandler->getRuleCountFromGroup($ruleGroup);
    }

    public function loadRuleGroups(RuleGroup $ruleGroup, int $offset = 0, ?int $limit = null): array
    {
        $data = $this->queryHandler->loadRuleGroupsData($ruleGroup, $offset, $limit);

        return $this->mapper->mapRuleGroups($data);
    }

    public function getRuleGroupCount(RuleGroup $ruleGroup): int
    {
        return $this->queryHandler->getRuleGroupCount($ruleGroup);
    }

    public function matchRules(string $targetType, $targetValue): array
    {
        $data = $this->queryHandler->matchRules($targetType, $targetValue);

        if (count($data) === 0) {
            return [];
        }

        return $this->mapper->mapRules($data);
    }

    public function loadTarget($targetId, int $status): Target
    {
        $targetId = $targetId instanceof UuidInterface ? $targetId->toString() : $targetId;
        $data = $this->queryHandler->loadTargetData($targetId, $status);

        if (count($data) === 0) {
            throw new NotFoundException('target', $targetId);
        }

        return $this->mapper->mapTargets($data)[0];
    }

    public function loadRuleTargets(Rule $rule): array
    {
        return $this->mapper->mapTargets(
            $this->queryHandler->loadRuleTargetsData($rule)
        );
    }

    public function getTargetCount(Rule $rule): int
    {
        return $this->queryHandler->getTargetCount($rule);
    }

    public function loadCondition($conditionId, int $status): Condition
    {
        $conditionId = $conditionId instanceof UuidInterface ? $conditionId->toString() : $conditionId;
        $data = $this->queryHandler->loadConditionData($conditionId, $status);

        if (count($data) === 0) {
            throw new NotFoundException('condition', $conditionId);
        }

        return $this->mapper->mapConditions($data)[0];
    }

    public function loadRuleConditions(Rule $rule): array
    {
        return $this->mapper->mapConditions(
            $this->queryHandler->loadRuleConditionsData($rule)
        );
    }

    public function loadRuleGroupConditions(RuleGroup $ruleGroup): array
    {
        return $this->mapper->mapConditions(
            $this->queryHandler->loadRuleGroupConditionsData($ruleGroup)
        );
    }

    public function ruleExists($ruleId, ?int $status = null): bool
    {
        $ruleId = $ruleId instanceof UuidInterface ? $ruleId->toString() : $ruleId;

        return $this->queryHandler->ruleExists($ruleId, $status);
    }

    public function createRule(RuleCreateStruct $ruleCreateStruct, RuleGroup $targetGroup): Rule
    {
        if (is_string($ruleCreateStruct->uuid) && $this->ruleExists($ruleCreateStruct->uuid)) {
            throw new BadStateException('uuid', 'Rule with provided UUID already exists.');
        }

        $layout = null;
        if ($ruleCreateStruct->layoutId !== null) {
            $layout = $this->layoutHandler->loadLayout($ruleCreateStruct->layoutId, Value::STATUS_PUBLISHED);
        }

        $newRule = Rule::fromArray(
            [
                'uuid' => is_string($ruleCreateStruct->uuid) ?
                    $ruleCreateStruct->uuid :
                    Uuid::uuid4()->toString(),
                'status' => $ruleCreateStruct->status,
                'ruleGroupUuid' => $targetGroup->uuid,
                'layoutUuid' => $layout instanceof Layout ? $layout->uuid : null,
                'enabled' => $ruleCreateStruct->enabled ? true : false,
                'priority' => $this->getRulePriority($ruleCreateStruct, $targetGroup),
                'comment' => trim($ruleCreateStruct->comment ?? ''),
            ]
        );

        return $this->queryHandler->createRule($newRule);
    }

    public function updateRule(Rule $rule, RuleUpdateStruct $ruleUpdateStruct): Rule
    {
        $updatedRule = clone $rule;

        if ($ruleUpdateStruct->layoutId !== null && !is_bool($ruleUpdateStruct->layoutId)) {
            $layout = $this->layoutHandler->loadLayout($ruleUpdateStruct->layoutId, Value::STATUS_PUBLISHED);

            $updatedRule->layoutUuid = $layout->uuid;
        } elseif ($ruleUpdateStruct->layoutId === false) {
            // Layout ID can be "false", to indicate removal of the linked layout
            $updatedRule->layoutUuid = null;
        }

        if (is_string($ruleUpdateStruct->comment)) {
            $updatedRule->comment = trim($ruleUpdateStruct->comment);
        }

        $this->queryHandler->updateRule($updatedRule);

        return $updatedRule;
    }

    public function updateRuleMetadata(Rule $rule, RuleMetadataUpdateStruct $ruleUpdateStruct): Rule
    {
        $updatedRule = clone $rule;

        if (is_int($ruleUpdateStruct->priority)) {
            $updatedRule->priority = $ruleUpdateStruct->priority;
        }

        if (is_bool($ruleUpdateStruct->enabled)) {
            $updatedRule->enabled = $ruleUpdateStruct->enabled;
        }

        $this->queryHandler->updateRuleData($updatedRule);

        return $updatedRule;
    }

    public function copyRule(Rule $rule, ?RuleGroup $targetGroup = null): Rule
    {
        // First copy the rule

        $copiedRule = clone $rule;

        unset($copiedRule->id);
        $copiedRule->uuid = Uuid::uuid4()->toString();

        if ($targetGroup !== null) {
            $rule->ruleGroupUuid = $targetGroup->uuid;
        }

        $copiedRule = $this->queryHandler->createRule($copiedRule);

        // Then copy rule targets

        $ruleTargets = $this->loadRuleTargets($rule);

        foreach ($ruleTargets as $ruleTarget) {
            $copiedTarget = clone $ruleTarget;

            unset($copiedTarget->id);
            $copiedTarget->uuid = Uuid::uuid4()->toString();

            $copiedTarget->ruleId = $copiedRule->id;
            $copiedTarget->ruleUuid = $copiedRule->uuid;

            $this->queryHandler->addTarget($copiedTarget);
        }

        // Then copy rule conditions

        $ruleConditions = $this->loadRuleConditions($rule);

        foreach ($ruleConditions as $ruleCondition) {
            $copiedCondition = clone $ruleCondition;

            unset($copiedCondition->id);
            $copiedCondition->uuid = Uuid::uuid4()->toString();

            $copiedCondition->ruleId = $copiedRule->id;
            $copiedCondition->ruleUuid = $copiedRule->uuid;

            $this->queryHandler->addCondition($copiedCondition);
        }

        return $copiedRule;
    }

    public function moveRule(Rule $rule, RuleGroup $targetGroup, ?int $newPriority = null): Rule
    {
        if ($rule->ruleGroupUuid === $targetGroup->uuid) {
            throw new BadStateException('targetGroup', 'Rule is already in specified target group.');
        }

        $this->queryHandler->moveRule($rule, $targetGroup, $newPriority);

        return $this->loadRule($rule->id, $rule->status);
    }

    public function createRuleStatus(Rule $rule, int $newStatus): Rule
    {
        // First copy the rule

        $copiedRule = clone $rule;
        $copiedRule->status = $newStatus;

        $copiedRule = $this->queryHandler->createRule($copiedRule);

        // Then copy rule targets

        $ruleTargets = $this->loadRuleTargets($rule);

        foreach ($ruleTargets as $ruleTarget) {
            $copiedTarget = clone $ruleTarget;
            $copiedTarget->status = $newStatus;

            $this->queryHandler->addTarget($copiedTarget);
        }

        // Then copy rule conditions

        $ruleConditions = $this->loadRuleConditions($rule);

        foreach ($ruleConditions as $ruleCondition) {
            $copiedCondition = clone $ruleCondition;
            $copiedCondition->status = $newStatus;

            $this->queryHandler->addCondition($copiedCondition);
        }

        return $copiedRule;
    }

    public function deleteRule(int $ruleId, ?int $status = null): void
    {
        $this->queryHandler->deleteRuleTargets($ruleId, $status);
        $this->queryHandler->deleteRuleConditions($ruleId, $status);
        $this->queryHandler->deleteRule($ruleId, $status);
    }

    public function ruleGroupExists($ruleGroupId, ?int $status = null): bool
    {
        $ruleGroupId = $ruleGroupId instanceof UuidInterface ? $ruleGroupId->toString() : $ruleGroupId;

        return $this->queryHandler->ruleGroupExists($ruleGroupId, $status);
    }

    public function createRuleGroup(RuleGroupCreateStruct $ruleGroupCreateStruct, ?RuleGroup $parentGroup = null): RuleGroup
    {
        if ($parentGroup === null && $this->queryHandler->ruleGroupExists(RuleGroup::ROOT_UUID)) {
            throw new BadStateException('parentGroup', 'Root rule group already exists.');
        }

        if (is_string($ruleGroupCreateStruct->uuid) && $this->ruleGroupExists($ruleGroupCreateStruct->uuid)) {
            throw new BadStateException('uuid', 'Rule group with provided UUID already exists.');
        }

        $newRuleGroup = RuleGroup::fromArray(
            [
                'uuid' => is_string($ruleGroupCreateStruct->uuid) ?
                    $ruleGroupCreateStruct->uuid :
                    Uuid::uuid4()->toString(),
                'status' => $ruleGroupCreateStruct->status,
                'depth' => $parentGroup !== null ? $parentGroup->depth + 1 : 0,
                'path' => $parentGroup !== null ? $parentGroup->path : '/',
                'parentId' => $parentGroup !== null ? $parentGroup->id : null,
                'parentUuid' => $parentGroup !== null ? $parentGroup->uuid : null,
                'enabled' => $ruleGroupCreateStruct->enabled ? true : false,
                'priority' => $parentGroup !== null ? $this->getRuleGroupPriority($ruleGroupCreateStruct, $parentGroup) : 0,
                'comment' => trim($ruleGroupCreateStruct->comment ?? ''),
            ]
        );

        if ($parentGroup === null) {
            // If the group has no parent, we make sure the the UUID is a NIL UUID. This, combined
            // with a check that only one group with no parent can be created, makes sure that we
            // only have one root group ever.
            $newRuleGroup->uuid = RuleGroup::ROOT_UUID;
        }

        return $this->queryHandler->createRuleGroup($newRuleGroup);
    }

    public function updateRuleGroup(RuleGroup $ruleGroup, RuleGroupUpdateStruct $ruleGroupUpdateStruct): RuleGroup
    {
        $updatedRuleGroup = clone $ruleGroup;

        if (is_string($ruleGroupUpdateStruct->comment)) {
            $updatedRuleGroup->comment = trim($ruleGroupUpdateStruct->comment);
        }

        $this->queryHandler->updateRuleGroup($updatedRuleGroup);

        return $updatedRuleGroup;
    }

    public function updateRuleGroupMetadata(RuleGroup $ruleGroup, RuleGroupMetadataUpdateStruct $ruleGroupUpdateStruct): RuleGroup
    {
        $updatedRuleGroup = clone $ruleGroup;

        if (is_int($ruleGroupUpdateStruct->priority)) {
            $updatedRuleGroup->priority = $ruleGroupUpdateStruct->priority;
        }

        if (is_bool($ruleGroupUpdateStruct->enabled)) {
            $updatedRuleGroup->enabled = $ruleGroupUpdateStruct->enabled;
        }

        $this->queryHandler->updateRuleGroupData($updatedRuleGroup);

        return $updatedRuleGroup;
    }

    public function copyRuleGroup(RuleGroup $ruleGroup, RuleGroup $targetGroup, bool $copyChildren = false): RuleGroup
    {
        if (str_starts_with($targetGroup->path, $ruleGroup->path)) {
            throw new BadStateException('targetGroup', 'Rule group cannot be copied below itself or its children.');
        }

        $newRuleGroup = clone $ruleGroup;

        unset($newRuleGroup->id);
        $newRuleGroup->uuid = Uuid::uuid4()->toString();

        $newRuleGroup->depth = $targetGroup->depth + 1;
        // This is only the initial path.
        // Full path is updated after we get the rule group ID.
        $newRuleGroup->path = $targetGroup->path;
        $newRuleGroup->parentId = $targetGroup->id;
        $newRuleGroup->parentUuid = $targetGroup->uuid;

        $copiedRuleGroup = $this->queryHandler->createRuleGroup($newRuleGroup);

        // Copy rule group conditions

        $ruleGroupConditions = $this->loadRuleGroupConditions($ruleGroup);

        foreach ($ruleGroupConditions as $ruleGroupCondition) {
            $copiedCondition = clone $ruleGroupCondition;

            unset($copiedCondition->id);
            $copiedCondition->uuid = Uuid::uuid4()->toString();

            $copiedCondition->ruleGroupId = $copiedRuleGroup->id;
            $copiedCondition->ruleGroupUuid = $copiedRuleGroup->uuid;

            $this->queryHandler->addCondition($copiedCondition);
        }

        if (!$copyChildren) {
            return $copiedRuleGroup;
        }

        foreach ($this->loadRulesFromGroup($ruleGroup) as $childRule) {
            $this->copyRule($childRule, $copiedRuleGroup);
        }

        foreach ($this->loadRuleGroups($ruleGroup) as $childGroup) {
            $this->copyRuleGroup($childGroup, $copiedRuleGroup, true);
        }

        return $copiedRuleGroup;
    }

    public function moveRuleGroup(RuleGroup $ruleGroup, RuleGroup $targetGroup, ?int $newPriority = null): RuleGroup
    {
        if ($ruleGroup->parentId === $targetGroup->id) {
            throw new BadStateException('targetGroup', 'Rule group is already in specified target group.');
        }

        if (str_starts_with($targetGroup->path, $ruleGroup->path)) {
            throw new BadStateException('targetGroup', 'Rule group cannot be moved below itself or its children.');
        }

        $this->queryHandler->moveRuleGroup($ruleGroup, $targetGroup, $newPriority);

        return $this->loadRuleGroup($ruleGroup->id, $ruleGroup->status);
    }

    public function createRuleGroupStatus(RuleGroup $ruleGroup, int $newStatus): RuleGroup
    {
        // First copy the rule group

        $copiedRuleGroup = clone $ruleGroup;
        $copiedRuleGroup->status = $newStatus;

        $copiedRuleGroup = $this->queryHandler->createRuleGroup($copiedRuleGroup);

        // Then copy rule group conditions

        $ruleGroupConditions = $this->loadRuleGroupConditions($ruleGroup);

        foreach ($ruleGroupConditions as $ruleGroupCondition) {
            $copiedCondition = clone $ruleGroupCondition;
            $copiedCondition->status = $newStatus;

            $this->queryHandler->addConditionToGroup($copiedCondition);
        }

        return $copiedRuleGroup;
    }

    public function deleteRuleGroup(int $ruleGroupId, ?int $status = null): void
    {
    }

    public function addTarget(Rule $rule, TargetCreateStruct $targetCreateStruct): Target
    {
        $newTarget = Target::fromArray(
            [
                'uuid' => Uuid::uuid4()->toString(),
                'status' => $rule->status,
                'ruleId' => $rule->id,
                'ruleUuid' => $rule->uuid,
                'type' => $targetCreateStruct->type,
                'value' => $targetCreateStruct->value,
            ]
        );

        return $this->queryHandler->addTarget($newTarget);
    }

    public function updateTarget(Target $target, TargetUpdateStruct $targetUpdateStruct): Target
    {
        $updatedTarget = clone $target;
        $updatedTarget->value = $targetUpdateStruct->value;

        $this->queryHandler->updateTarget($updatedTarget);

        return $updatedTarget;
    }

    public function deleteTarget(Target $target): void
    {
        $this->queryHandler->deleteTarget($target->id, $target->status);
    }

    public function addCondition(Rule $rule, ConditionCreateStruct $conditionCreateStruct): Condition
    {
        $newCondition = Condition::fromArray(
            [
                'uuid' => Uuid::uuid4()->toString(),
                'status' => $rule->status,
                'ruleId' => $rule->id,
                'ruleUuid' => $rule->uuid,
                'type' => $conditionCreateStruct->type,
                'value' => $conditionCreateStruct->value,
            ]
        );

        return $this->queryHandler->addCondition($newCondition);
    }

    public function addConditionToGroup(RuleGroup $ruleGroup, ConditionCreateStruct $conditionCreateStruct): Condition
    {
        $newCondition = Condition::fromArray(
            [
                'uuid' => Uuid::uuid4()->toString(),
                'status' => $rule->status,
                'ruleId' => $rule->id,
                'ruleUuid' => $rule->uuid,
                'type' => $conditionCreateStruct->type,
                'value' => $conditionCreateStruct->value,
            ]
        );

        return $this->queryHandler->addCondition($newCondition);
    }

    public function updateCondition(Condition $condition, ConditionUpdateStruct $conditionUpdateStruct): Condition
    {
        $updatedCondition = clone $condition;

        $updatedCondition->value = $conditionUpdateStruct->value;

        $this->queryHandler->updateCondition($updatedCondition);

        return $updatedCondition;
    }

    public function deleteCondition(Condition $condition): void
    {
        $this->queryHandler->deleteCondition($condition->id, $condition->status);
    }

    /**
     * Returns the rule priority when creating a new rule.
     *
     * If priority is specified in the struct, it is used automatically. Otherwise,
     * the returned priority is the lowest available priority subtracted by 10 (to allow
     * inserting rules in between).
     *
     * If no rules exist, priority is 0.
     */
    private function getRulePriority(RuleCreateStruct $ruleCreateStruct, RuleGroup $targetGroup): int
    {
        if (is_int($ruleCreateStruct->priority)) {
            return $ruleCreateStruct->priority;
        }

        $lowestRulePriority = $this->queryHandler->getLowestRulePriority($targetGroup);
        if ($lowestRulePriority !== null) {
            return $lowestRulePriority - 10;
        }

        return 0;
    }

    private function getRuleGroupPriority(RuleGroupCreateStruct $ruleGroupCreateStruct, RuleGroup $parentGroup): int
    {
        if (is_int($ruleGroupCreateStruct->priority)) {
            return $ruleGroupCreateStruct->priority;
        }

        $lowestRuleGroupPriority = $this->queryHandler->getLowestRuleGroupPriority($parentGroup);
        if ($lowestRuleGroupPriority !== null) {
            return $lowestRuleGroupPriority - 10;
        }

        return 0;
    }
}
