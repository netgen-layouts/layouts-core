<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\QueryHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Types;
use Netgen\Layouts\Exception\Persistence\TargetHandlerException;
use Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelperInterface;
use Netgen\Layouts\Persistence\Values\Layout\Layout;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Condition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Rule;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Target;
use Netgen\Layouts\Persistence\Values\Value;
use Psr\Container\ContainerInterface;
use function is_array;
use function json_encode;

final class LayoutResolverQueryHandler extends QueryHandler
{
    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $targetHandlers;

    public function __construct(
        Connection $connection,
        ConnectionHelperInterface $connectionHelper,
        ContainerInterface $targetHandlers
    ) {
        $this->targetHandlers = $targetHandlers;

        parent::__construct($connection, $connectionHelper);
    }

    /**
     * Returns all data for specified rule.
     *
     * @param int|string $ruleId
     *
     * @return mixed[]
     */
    public function loadRuleData($ruleId, int $status): array
    {
        $query = $this->getRuleSelectQuery();

        $this->applyIdCondition($query, $ruleId, 'r.id', 'r.uuid');
        $this->applyStatusCondition($query, $status, 'r.status');

        return $query->execute()->fetchAllAssociative();
    }

    /**
     * Returns all data for all rules.
     *
     * @return mixed[]
     */
    public function loadRulesData(int $status, ?Layout $layout = null, int $offset = 0, ?int $limit = null): array
    {
        $query = $this->getRuleSelectQuery();

        if ($layout instanceof Layout) {
            $query->andWhere(
                $query->expr()->eq('layout_uuid', ':layout_uuid')
            )
            ->setParameter('layout_uuid', $layout->uuid, Types::STRING);
        }

        $query->addOrderBy('rd.priority', 'DESC');

        $this->applyStatusCondition($query, $status, 'r.status');
        $this->applyOffsetAndLimit($query, $offset, $limit);

        return $query->execute()->fetchAllAssociative();
    }

    /**
     * Returns the number of rules.
     */
    public function getRuleCount(int $ruleStatus, ?Layout $layout = null): int
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('nglayouts_rule');

        if ($layout instanceof Layout) {
            $query->andWhere(
                $query->expr()->eq('layout_uuid', ':layout_uuid')
            )
            ->setParameter('layout_uuid', $layout->uuid, Types::STRING);
        }

        $this->applyStatusCondition($query, $ruleStatus);

        $data = $query->execute()->fetchAllAssociative();

        return (int) ($data[0]['count'] ?? 0);
    }

    /**
     * Returns all data for all rules located in provided group.
     *
     * @return mixed[]
     */
    public function loadRulesFromGroupData(RuleGroup $ruleGroup, int $offset = 0, ?int $limit = null): array
    {
        $query = $this->getRuleSelectQuery();

        $query->andWhere(
            $query->expr()->eq('rule_group_uuid', ':rule_group_uuid')
        )
        ->setParameter('rule_group_uuid', $ruleGroup->uuid, Types::STRING);

        $query->addOrderBy('rd.priority', 'DESC');

        $this->applyStatusCondition($query, $ruleGroup->status, 'r.status');
        $this->applyOffsetAndLimit($query, $offset, $limit);

        return $query->execute()->fetchAllAssociative();
    }

    /**
     * Returns the number of rules in provided group.
     */
    public function getRuleCountFromGroup(RuleGroup $ruleGroup): int
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('nglayouts_rule_group');

        $query->andWhere(
            $query->expr()->eq('rule_group_uuid', ':rule_group_uuid')
        )
        ->setParameter('rule_group_uuid', $ruleGroup->uuid, Types::STRING);

        $this->applyStatusCondition($query, $ruleGroup->status);

        $data = $query->execute()->fetchAllAssociative();

        return (int) ($data[0]['count'] ?? 0);
    }

    /**
     * Returns all data for specified rule group.
     *
     * @param int|string $ruleGroupId
     *
     * @return mixed[]
     */
    public function loadRuleGroupData($ruleGroupId, int $status): array
    {
        $query = $this->getRuleGroupSelectQuery();

        $this->applyIdCondition($query, $ruleGroupId, 'rg.id', 'rg.uuid');
        $this->applyStatusCondition($query, $status, 'rg.status');

        return $query->execute()->fetchAllAssociative();
    }

    /**
     * Returns all data for all rule groups.
     *
     * @return mixed[]
     */
    public function loadRuleGroupsData(RuleGroup $ruleGroup, int $offset = 0, ?int $limit = null): array
    {
        $query = $this->getRuleGroupSelectQuery();

        $query->addOrderBy('rgd.priority', 'DESC');

        $this->applyStatusCondition($query, $ruleGroup->status, 'rg.status');
        $this->applyOffsetAndLimit($query, $offset, $limit);

        return $query->execute()->fetchAllAssociative();
    }

    /**
     * Returns the number of rule groups.
     */
    public function getRuleGroupCount(RuleGroup $ruleGroup): int
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('nglayouts_rule_group');

        $this->applyStatusCondition($query, $ruleGroup->status);

        $data = $query->execute()->fetchAllAssociative();

        return (int) ($data[0]['count'] ?? 0);
    }

    /**
     * Returns all rule data for rules that match specified target type and value.
     *
     * @param mixed $targetValue
     *
     * @return mixed[]
     */
    public function matchRules(string $targetType, $targetValue): array
    {
        $query = $this->getRuleSelectQuery();
        $query
            ->innerJoin(
                'r',
                'nglayouts_rule_target',
                'rt',
                $query->expr()->eq('r.id', 'rt.rule_id')
            )
            ->where(
                $query->expr()->eq('rd.enabled', ':enabled'),
                $query->expr()->eq('rt.type', ':target_type')
            )
            ->setParameter('target_type', $targetType, Types::STRING)
            ->setParameter('enabled', true, Types::BOOLEAN)
            ->addOrderBy('rd.priority', 'DESC');

        $this->applyStatusCondition($query, Value::STATUS_PUBLISHED, 'r.status');
        $this->applyStatusCondition($query, Value::STATUS_PUBLISHED, 'rt.status');

        $targetHandler = $this->getTargetHandler($targetType);
        $targetHandler->handleQuery($query, $targetValue);

        return $query->execute()->fetchAllAssociative();
    }

    /**
     * Returns all data for specified target.
     *
     * @param int|string $targetId
     *
     * @return mixed[]
     */
    public function loadTargetData($targetId, int $status): array
    {
        $query = $this->getTargetSelectQuery();

        $this->applyIdCondition($query, $targetId, 't.id', 't.uuid');
        $this->applyStatusCondition($query, $status, 't.status');

        return $query->execute()->fetchAllAssociative();
    }

    /**
     * Returns all data for all rule targets.
     *
     * @return mixed[]
     */
    public function loadRuleTargetsData(Rule $rule): array
    {
        $query = $this->getTargetSelectQuery();
        $query->where(
            $query->expr()->eq('t.rule_id', ':rule_id')
        )
        ->setParameter('rule_id', $rule->id, Types::INTEGER)
        ->orderBy('t.id', 'ASC');

        $this->applyStatusCondition($query, $rule->status, 't.status');

        return $query->execute()->fetchAllAssociative();
    }

    /**
     * Returns the number of targets within the rule.
     */
    public function getTargetCount(Rule $rule): int
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('nglayouts_rule_target')
            ->where(
                $query->expr()->eq('rule_id', ':rule_id')
            )
            ->setParameter('rule_id', $rule->id, Types::INTEGER);

        $this->applyStatusCondition($query, $rule->status);

        $data = $query->execute()->fetchAllAssociative();

        return (int) ($data[0]['count'] ?? 0);
    }

    /**
     * Returns all data for specified condition.
     *
     * @param int|string $conditionId
     *
     * @return mixed[]
     */
    public function loadConditionData($conditionId, int $status): array
    {
        $query = $this->getConditionSelectQuery();

        $this->applyIdCondition($query, $conditionId, 'c.id', 'c.uuid');
        $this->applyStatusCondition($query, $status, 'c.status');

        return $query->execute()->fetchAllAssociative();
    }

    /**
     * Returns all data for for all rule conditions.
     *
     * @return mixed[]
     */
    public function loadRuleConditionsData(Rule $rule): array
    {
        $query = $this->getConditionSelectQuery();
        $query->where(
            $query->expr()->eq('c.rule_id', ':rule_id')
        )
        ->setParameter('rule_id', $rule->id, Types::INTEGER)
        ->orderBy('c.id', 'ASC');

        $this->applyStatusCondition($query, $rule->status, 'c.status');

        return $query->execute()->fetchAllAssociative();
    }

    /**
     * Returns all data for for all rule group conditions.
     *
     * @return mixed[]
     */
    public function loadRuleGroupConditionsData(RuleGroup $ruleGroup): array
    {
        $query = $this->getConditionSelectQuery();
        $query->where(
            $query->expr()->eq('c.rule_group_id', ':rule_group_id')
        )
        ->setParameter('rule_group_id', $rule->id, Types::INTEGER)
        ->orderBy('c.id', 'ASC');

        $this->applyStatusCondition($query, $rule->status, 'c.status');

        return $query->execute()->fetchAllAssociative();
    }

    /**
     * Returns if the specified rule exists.
     *
     * @param int|string $ruleId
     */
    public function ruleExists($ruleId, ?int $status = null): bool
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('nglayouts_rule');

        $this->applyIdCondition($query, $ruleId);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $data = $query->execute()->fetchAllAssociative();

        return (int) ($data[0]['count'] ?? 0) > 0;
    }

    /**
     * Returns if the specified rule group exists.
     *
     * @param int|string $ruleGroupId
     */
    public function ruleGroupExists($ruleGroupId, ?int $status = null): bool
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('nglayouts_rule_group');

        $this->applyIdCondition($query, $ruleGroupId);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $data = $query->execute()->fetchAllAssociative();

        return (int) ($data[0]['count'] ?? 0) > 0;
    }

    /**
     * Returns the lowest priority from the list of all the rules.
     */
    public function getLowestRulePriority(RuleGroup $targetGroup): ?int
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('rd.priority')
            ->from('nglayouts_rule_data')
            ->innerJoin(
                'rd',
                'nglayouts_rule',
                'r',
                $query->expr()->eq('rd.rule_id', 'r.id')
            )
            ->where(
                $query->expr()->eq('r.rule_group_uuid', ':rule_group_uuid')
            )
            ->setParameter('rule_group_uuid', $targetGroup->uuid, Types::INTEGER);

        $query->addOrderBy('rd.priority', 'ASC');
        $this->applyOffsetAndLimit($query, 0, 1);

        $data = $query->execute()->fetchAllAssociative();

        return isset($data[0]['priority']) ? (int) $data[0]['priority'] : null;
    }

    /**
     * Returns the lowest priority from the list of all the rule groups in provided parent group.
     */
    public function getLowestRuleGroupPriority(RuleGroup $parentGroup): ?int
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('rgd.priority')
            ->from('nglayouts_rule_group_data', 'rgd')
            ->innerJoin(
                'rgd',
                'nglayouts_rule_group',
                'rg',
                $query->expr()->eq('rgd.rule_group_id', 'rg.id')
            )
            ->where(
                $query->expr()->eq('rg.parent_id', ':parent_id')
            )
            ->setParameter('parent_id', $parentGroup->id, Types::INTEGER);

        $query->addOrderBy('rgd.priority', 'ASC');
        $this->applyOffsetAndLimit($query, 0, 1);

        $data = $query->execute()->fetchAllAssociative();

        return isset($data[0]['priority']) ? (int) $data[0]['priority'] : null;
    }

    /**
     * Creates a rule.
     */
    public function createRule(Rule $rule): Rule
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('nglayouts_rule')
            ->values(
                [
                    'id' => ':id',
                    'uuid' => ':uuid',
                    'status' => ':status',
                    'rule_group_uuid' => ':rule_group_uuid',
                    'layout_uuid' => ':layout_uuid',
                    'comment' => ':comment',
                ]
            )
            ->setValue('id', $rule->id ?? $this->connectionHelper->nextId('nglayouts_rule'))
            ->setParameter('uuid', $rule->uuid, Types::STRING)
            ->setParameter('status', $rule->status, Types::INTEGER)
            ->setParameter('rule_group_uuid', $rule->ruleGroupUuid, Types::STRING)
            ->setParameter('layout_uuid', $rule->layoutUuid, Types::STRING)
            ->setParameter('comment', $rule->comment, Types::STRING);

        $query->execute();

        if (!isset($rule->id)) {
            $rule->id = (int) $this->connectionHelper->lastId('nglayouts_rule');

            $query = $this->connection->createQueryBuilder()
                ->insert('nglayouts_rule_data')
                ->values(
                    [
                        'rule_id' => ':rule_id',
                        'enabled' => ':enabled',
                        'priority' => ':priority',
                    ]
                )
                ->setParameter('rule_id', $rule->id, Types::INTEGER)
                ->setParameter('enabled', $rule->enabled, Types::BOOLEAN)
                ->setParameter('priority', $rule->priority, Types::INTEGER);

            $query->execute();
        }

        return $rule;
    }

    /**
     * Updates a rule.
     */
    public function updateRule(Rule $rule): void
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('nglayouts_rule')
            ->set('uuid', ':uuid')
            ->set('layout_uuid', ':layout_uuid')
            ->set('comment', ':comment')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $rule->id, Types::INTEGER)
            ->setParameter('uuid', $rule->uuid, Types::STRING)
            ->setParameter('layout_uuid', $rule->layoutUuid, Types::STRING)
            ->setParameter('comment', $rule->comment, Types::STRING);

        $this->applyStatusCondition($query, $rule->status);

        $query->execute();
    }

    /**
     * Updates rule data which is independent of statuses.
     */
    public function updateRuleData(Rule $rule): void
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('nglayouts_rule_data')
            ->set('enabled', ':enabled')
            ->set('priority', ':priority')
            ->where(
                $query->expr()->eq('rule_id', ':rule_id')
            )
            ->setParameter('rule_id', $rule->id, Types::INTEGER)
            ->setParameter('enabled', $rule->enabled, Types::BOOLEAN)
            ->setParameter('priority', $rule->priority, Types::INTEGER);

        $query->execute();
    }

    /**
     * Moves a rule.
     */
    public function moveRule(Rule $rule, RuleGroup $targetGroup, ?int $newPriority = null): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->update('nglayouts_rule')
            ->set('rule_group_uuid', ':rule_group_uuid')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $rule->id, Types::INTEGER)
            ->setParameter('rule_group_uuid', $targetGroup->uuid, Types::INTEGER);

        $this->applyStatusCondition($query, $rule->status);

        $query->execute();

        if ($newPriority !== null) {
            $query = $this->connection->createQueryBuilder();
            $query
                ->update('nglayouts_rule_data')
                ->set('priority', ':priority')
                ->where(
                    $query->expr()->eq('rule_id', ':rule_id')
                )
                ->setParameter('rule_id', $rule->id, Types::INTEGER)
                ->setParameter('priority', $newPriority, Types::INTEGER);

            $query->execute();
        }

        $query->execute();
    }

    /**
     * Creates a rule group.
     */
    public function createRuleGroup(RuleGroup $ruleGroup, bool $updatePath = true): RuleGroup
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('nglayouts_rule_group')
            ->values(
                [
                    'id' => ':id',
                    'uuid' => ':uuid',
                    'status' => ':status',
                    'depth' => ':depth',
                    'path' => ':path',
                    'parent_id' => ':parent_id',
                    'comment' => ':comment',
                ]
            )
            ->setValue('id', $ruleGroup->id ?? $this->connectionHelper->nextId('nglayouts_rule_group'))
            ->setParameter('uuid', $ruleGroup->uuid, Types::STRING)
            ->setParameter('status', $ruleGroup->status, Types::INTEGER)
            ->setParameter('depth', $ruleGroup->depth, Types::STRING)
            // Materialized path is updated after rule group is created
            ->setParameter('path', $ruleGroup->path, Types::STRING)
            ->setParameter('parent_id', $ruleGroup->parentId, Types::INTEGER)
            ->setParameter('comment', $ruleGroup->comment, Types::STRING);

        $query->execute();

        if (!isset($ruleGroup->id)) {
            $ruleGroup->id = (int) $this->connectionHelper->lastId('nglayouts_rule_group');

            $query = $this->connection->createQueryBuilder()
                ->insert('nglayouts_rule_group_data')
                ->values(
                    [
                        'rule_group_id' => ':rule_group_id',
                        'enabled' => ':enabled',
                        'priority' => ':priority',
                    ]
                )
                ->setParameter('rule_group_id', $ruleGroup->id, Types::INTEGER)
                ->setParameter('enabled', $ruleGroup->enabled, Types::BOOLEAN)
                ->setParameter('priority', $ruleGroup->priority, Types::INTEGER);

            $query->execute();
        }

        if (!$updatePath) {
            return $ruleGroup;
        }

        // Update materialized path only after creating the rule group, when we have the ID

        $ruleGroup->path = $ruleGroup->path . $ruleGroup->id . '/';

        $query = $this->connection->createQueryBuilder();
        $query
            ->update('nglayouts_rule_group')
            ->set('path', ':path')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $ruleGroup->id, Types::INTEGER)
            ->setParameter('path', $ruleGroup->path, Types::STRING);

        $this->applyStatusCondition($query, $ruleGroup->status);

        $query->execute();

        return $ruleGroup;
    }

    /**
     * Updates a rule group.
     */
    public function updateRuleGroup(RuleGroup $ruleGroup): void
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('nglayouts_rule_group')
            ->set('uuid', ':uuid')
            ->set('depth', ':depth')
            ->set('path', ':path')
            ->set('parent_id', ':parent_id')
            ->set('comment', ':comment')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $ruleGroup->id, Types::INTEGER)
            ->setParameter('uuid', $ruleGroup->uuid, Types::STRING)
            ->setParameter('depth', $ruleGroup->depth, Types::STRING)
            ->setParameter('path', $ruleGroup->path, Types::STRING)
            ->setParameter('parent_id', $ruleGroup->parentId, Types::INTEGER)
            ->setParameter('comment', $ruleGroup->comment, Types::STRING);

        $this->applyStatusCondition($query, $ruleGroup->status);

        $query->execute();
    }

    /**
     * Updates rule group data which is independent of statuses.
     */
    public function updateRuleGroupData(RuleGroup $ruleGroup): void
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('nglayouts_rule_group_data')
            ->set('enabled', ':enabled')
            ->set('priority', ':priority')
            ->where(
                $query->expr()->eq('rule_group_id', ':rule_group_id')
            )
            ->setParameter('rule_group_id', $ruleGroup->id, Types::INTEGER)
            ->setParameter('enabled', $ruleGroup->enabled, Types::BOOLEAN)
            ->setParameter('priority', $ruleGroup->priority, Types::INTEGER);

        $query->execute();
    }

    /**
     * Moves a rule group.
     */
    public function moveRuleGroup(RuleGroup $ruleGroup, RuleGroup $targetGroup, ?int $newPriority = null): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->update('nglayouts_rule_group')
            ->set('parent_id', ':parent_id')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $ruleGroup->id, Types::INTEGER)
            ->setParameter('parent_id', $targetGroup->id, Types::INTEGER);

        $this->applyStatusCondition($query, $ruleGroup->status);

        $query->execute();

        $depthDifference = $ruleGroup->depth - ($targetGroup->depth + 1);

        $query = $this->connection->createQueryBuilder();

        $query
            ->update('nglayouts_rule_group')
            ->set('depth', 'depth - :depth_difference')
            ->set('path', 'replace(path, :old_path, :new_path)')
            ->where(
                $query->expr()->like('path', ':path')
            )
            ->setParameter('depth_difference', $depthDifference, Types::INTEGER)
            ->setParameter('old_path', $ruleGroup->path, Types::STRING)
            ->setParameter('new_path', $targetGroup->path . $ruleGroup->id . '/', Types::STRING)
            ->setParameter('path', $ruleGroup->path . '%', Types::STRING);

        $this->applyStatusCondition($query, $ruleGroup->status);

        if ($newPriority !== null) {
            $query = $this->connection->createQueryBuilder();
            $query
                ->update('nglayouts_rule_group_data')
                ->set('priority', ':priority')
                ->where(
                    $query->expr()->eq('rule_group_id', ':rule_group_id')
                )
                ->setParameter('rule_group_id', $ruleGroup->id, Types::INTEGER)
                ->setParameter('priority', $newPriority, Types::INTEGER);

            $query->execute();
        }

        $query->execute();
    }

    /**
     * Deletes all rule targets.
     */
    public function deleteRuleTargets(int $ruleId, ?int $status = null): void
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->delete('nglayouts_rule_target')
            ->where(
                $query->expr()->eq('rule_id', ':rule_id')
            )
            ->setParameter('rule_id', $ruleId, Types::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $query->execute();
    }

    /**
     * Delete all rule conditions.
     */
    public function deleteRuleConditions(int $ruleId, ?int $status = null): void
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->delete('nglayouts_rule_condition')
            ->where(
                $query->expr()->eq('rule_id', ':rule_id')
            )
            ->setParameter('rule_id', $ruleId, Types::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $query->execute();
    }

    /**
     * Deletes a rule.
     */
    public function deleteRule(int $ruleId, ?int $status = null): void
    {
        $query = $this->connection->createQueryBuilder();
        $query->delete('nglayouts_rule')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $ruleId, Types::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $query->execute();

        if (!$this->ruleExists($ruleId)) {
            $query = $this->connection->createQueryBuilder();
            $query->delete('nglayouts_rule_data')
                ->where(
                    $query->expr()->eq('rule_id', ':rule_id')
                )
                ->setParameter('rule_id', $ruleId, Types::INTEGER);

            $query->execute();
        }
    }

    /**
     * Adds a target.
     */
    public function addTarget(Target $target): Target
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('nglayouts_rule_target')
            ->values(
                [
                    'id' => ':id',
                    'uuid' => ':uuid',
                    'status' => ':status',
                    'rule_id' => ':rule_id',
                    'type' => ':type',
                    'value' => ':value',
                ]
            )
            ->setValue('id', $target->id ?? $this->connectionHelper->nextId('nglayouts_rule_target'))
            ->setParameter('uuid', $target->uuid, Types::STRING)
            ->setParameter('status', $target->status, Types::INTEGER)
            ->setParameter('rule_id', $target->ruleId, Types::INTEGER)
            ->setParameter('type', $target->type, Types::STRING)
            ->setParameter('value', $target->value, is_array($target->value) ? Types::JSON : Types::STRING);

        $query->execute();

        if (!isset($target->id)) {
            $target->id = (int) $this->connectionHelper->lastId('nglayouts_rule_target');
        }

        return $target;
    }

    /**
     * Updates a target.
     */
    public function updateTarget(Target $target): void
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('nglayouts_rule_target')
            ->set('uuid', ':uuid')
            ->set('rule_id', ':rule_id')
            ->set('type', ':type')
            ->set('value', ':value')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $target->id, Types::INTEGER)
            ->setParameter('uuid', $target->uuid, Types::STRING)
            ->setParameter('rule_id', $target->ruleId, Types::INTEGER)
            ->setParameter('type', $target->type, Types::STRING)
            ->setParameter('value', $target->value, is_array($target->value) ? Types::JSON : Types::STRING);

        $this->applyStatusCondition($query, $target->status);

        $query->execute();
    }

    /**
     * Deletes a target.
     */
    public function deleteTarget(int $targetId, int $status): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('nglayouts_rule_target')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $targetId, Types::INTEGER);

        $this->applyStatusCondition($query, $status);

        $query->execute();
    }

    /**
     * Adds a condition.
     */
    public function addCondition(Condition $condition): Condition
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('nglayouts_rule_condition')
            ->values(
                [
                    'id' => ':id',
                    'uuid' => ':uuid',
                    'status' => ':status',
                    'rule_id' => ':rule_id',
                    'type' => ':type',
                    'value' => ':value',
                ]
            )
            ->setValue('id', $condition->id ?? $this->connectionHelper->nextId('nglayouts_rule_condition'))
            ->setParameter('uuid', $condition->uuid, Types::STRING)
            ->setParameter('status', $condition->status, Types::INTEGER)
            ->setParameter('rule_id', $condition->ruleId, Types::INTEGER)
            ->setParameter('type', $condition->type, Types::STRING)
            ->setParameter('value', json_encode($condition->value), Types::STRING);

        $query->execute();

        if (!isset($condition->id)) {
            $condition->id = (int) $this->connectionHelper->lastId('nglayouts_rule_condition');
        }

        return $condition;
    }

    /**
     * Updates a condition.
     */
    public function updateCondition(Condition $condition): void
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('nglayouts_rule_condition')
            ->set('uuid', ':uuid')
            ->set('rule_id', ':rule_id')
            ->set('type', ':type')
            ->set('value', ':value')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $condition->id, Types::INTEGER)
            ->setParameter('uuid', $condition->uuid, Types::STRING)
            ->setParameter('rule_id', $condition->ruleId, Types::INTEGER)
            ->setParameter('type', $condition->type, Types::STRING)
            ->setParameter('value', json_encode($condition->value), Types::STRING);

        $this->applyStatusCondition($query, $condition->status);

        $query->execute();
    }

    /**
     * Deletes a condition.
     */
    public function deleteCondition(int $conditionId, int $status): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('nglayouts_rule_condition')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $conditionId, Types::INTEGER);

        $this->applyStatusCondition($query, $status);

        $query->execute();
    }

    /**
     * Builds and returns a rule database SELECT query.
     */
    private function getRuleSelectQuery(): QueryBuilder
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT r.*', 'rd.*, l.uuid AS layout_uuid')
            ->from('nglayouts_rule', 'r')
            ->innerJoin(
                'r',
                'nglayouts_rule_data',
                'rd',
                $query->expr()->eq('rd.rule_id', 'r.id')
            )->leftJoin(
                'r',
                'nglayouts_layout',
                'l',
                $query->expr()->and(
                    $query->expr()->eq('r.layout_uuid', 'l.uuid'),
                    $query->expr()->eq('l.status', Value::STATUS_PUBLISHED)
                )
            );

        return $query;
    }

    /**
     * Builds and returns a rule group database SELECT query.
     */
    private function getRuleGroupSelectQuery(): QueryBuilder
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT rg.*', 'rgd.*')
            ->from('nglayouts_rule_group', 'rg')
            ->innerJoin(
                'rg',
                'nglayouts_rule_group_data',
                'rgd',
                $query->expr()->eq('rgd.rule_id', 'rg.id')
            );

        return $query;
    }

    /**
     * Builds and returns a target database SELECT query.
     */
    private function getTargetSelectQuery(): QueryBuilder
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT t.*, r.uuid AS rule_uuid')
            ->from('nglayouts_rule_target', 't')
            ->innerJoin(
                't',
                'nglayouts_rule',
                'r',
                $query->expr()->and(
                    $query->expr()->eq('r.id', 't.rule_id'),
                    $query->expr()->eq('r.status', 't.status')
                )
            );

        return $query;
    }

    /**
     * Builds and returns a condition database SELECT query.
     */
    private function getConditionSelectQuery(): QueryBuilder
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT c.*, r.uuid AS rule_uuid')
            ->from('nglayouts_rule_condition', 'c')
            ->innerJoin(
                'c',
                'nglayouts_rule',
                'r',
                $query->expr()->and(
                    $query->expr()->eq('r.id', 'c.rule_id'),
                    $query->expr()->eq('r.status', 'c.status')
                )
            );

        return $query;
    }

    /**
     * Returns the target handler for provided target type from the collection.
     *
     * @throws \Netgen\Layouts\Exception\Persistence\TargetHandlerException If the target handler does not exist or is not of correct type
     */
    private function getTargetHandler(string $targetType): TargetHandlerInterface
    {
        if (!$this->targetHandlers->has($targetType)) {
            throw TargetHandlerException::noTargetHandler('Doctrine', $targetType);
        }

        $targetHandler = $this->targetHandlers->get($targetType);
        if (!$targetHandler instanceof TargetHandlerInterface) {
            throw TargetHandlerException::noTargetHandler('Doctrine', $targetType);
        }

        return $targetHandler;
    }
}
