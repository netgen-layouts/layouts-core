<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\Handler;

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

    public function loadRules(int $status, ?Layout $layout = null, int $offset = 0, ?int $limit = null): array
    {
        $data = $this->queryHandler->loadRulesData($status, $layout, $offset, $limit);

        return $this->mapper->mapRules($data);
    }

    public function getRuleCount(?Layout $layout = null): int
    {
        return $this->queryHandler->getRuleCount(Value::STATUS_PUBLISHED, $layout);
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

    public function ruleExists($ruleId, int $status): bool
    {
        $ruleId = $ruleId instanceof UuidInterface ? $ruleId->toString() : $ruleId;

        return $this->queryHandler->ruleExists($ruleId, $status);
    }

    public function createRule(RuleCreateStruct $ruleCreateStruct): Rule
    {
        $layout = null;
        if ($ruleCreateStruct->layoutId !== null) {
            $layout = $this->layoutHandler->loadLayout($ruleCreateStruct->layoutId, Value::STATUS_PUBLISHED);
        }

        $newRule = Rule::fromArray(
            [
                'uuid' => Uuid::uuid4()->toString(),
                'status' => $ruleCreateStruct->status,
                'layoutId' => $layout instanceof Layout ? $layout->id : null,
                'layoutUuid' => $layout instanceof Layout ? $layout->uuid : null,
                'enabled' => $ruleCreateStruct->enabled ? true : false,
                'priority' => $this->getRulePriority($ruleCreateStruct),
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

            $updatedRule->layoutId = $layout->id;
            $updatedRule->layoutUuid = $layout->uuid;
        } elseif ($ruleUpdateStruct->layoutId === false) {
            // Layout ID can be "false", to indicate removal of the linked layout
            $updatedRule->layoutId = null;
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

    public function copyRule(Rule $rule): Rule
    {
        // First copy the rule

        $copiedRule = clone $rule;
        $copiedRule->id = null;
        $copiedRule->uuid = Uuid::uuid4()->toString();

        $copiedRule = $this->queryHandler->createRule($copiedRule);

        // Then copy rule targets

        $ruleTargets = $this->loadRuleTargets($rule);

        foreach ($ruleTargets as $ruleTarget) {
            $copiedTarget = clone $ruleTarget;
            $copiedTarget->id = null;
            $copiedTarget->uuid = Uuid::uuid4()->toString();

            $copiedTarget->ruleId = $copiedRule->id;
            $copiedTarget->ruleUuid = $copiedRule->uuid;

            $this->queryHandler->addTarget($copiedTarget);
        }

        // Then copy rule conditions

        $ruleConditions = $this->loadRuleConditions($rule);

        foreach ($ruleConditions as $ruleCondition) {
            $copiedCondition = clone $ruleCondition;
            $copiedCondition->id = null;
            $copiedCondition->uuid = Uuid::uuid4()->toString();

            $copiedCondition->ruleId = $copiedRule->id;
            $copiedCondition->ruleUuid = $copiedRule->uuid;

            $this->queryHandler->addCondition($copiedCondition);
        }

        return $copiedRule;
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
    private function getRulePriority(RuleCreateStruct $ruleCreateStruct): int
    {
        if (is_int($ruleCreateStruct->priority)) {
            return $ruleCreateStruct->priority;
        }

        $lowestRulePriority = $this->queryHandler->getLowestRulePriority();
        if ($lowestRulePriority !== null) {
            return $lowestRulePriority - 10;
        }

        return 0;
    }
}
