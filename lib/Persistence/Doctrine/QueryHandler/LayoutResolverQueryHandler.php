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
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroupCondition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Target;
use Netgen\Layouts\Persistence\Values\Value;
use Psr\Container\ContainerInterface;

use function array_column;
use function array_map;
use function count;
use function is_array;
use function json_encode;
use function min;

final class LayoutResolverQueryHandler extends QueryHandler
{
    private ContainerInterface $targetHandlers;

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
     * Returns all data for all rules mapped to provided layout.
     *
     * @return mixed[]
     */
    public function loadRulesForLayoutData(Layout $layout, int $offset = 0, ?int $limit = null, bool $ascending = false): array
    {
        $query = $this->getRuleSelectQuery();

        $query->where(
            $query->expr()->eq('layout_uuid', ':layout_uuid'),
        )
        ->setParameter('layout_uuid', $layout->uuid, Types::STRING)
        ->addOrderBy('rd.priority', $ascending ? 'ASC' : 'DESC');

        $this->applyStatusCondition($query, $layout->status, 'r.status');
        $this->applyOffsetAndLimit($query, $offset, $limit);

        return $query->execute()->fetchAllAssociative();
    }

    /**
     * Returns the number of rules mapped to provided layout.
     */
    public function getRuleCountForLayout(int $ruleStatus, Layout $layout): int
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('nglayouts_rule')
            ->where(
                $query->expr()->eq('layout_uuid', ':layout_uuid'),
            )
            ->setParameter('layout_uuid', $layout->uuid, Types::STRING);

        $this->applyStatusCondition($query, $ruleStatus);

        $data = $query->execute()->fetchAllAssociative();

        return (int) ($data[0]['count'] ?? 0);
    }

    /**
     * Returns all data for all rules located in provided group.
     *
     * @return mixed[]
     */
    public function loadRulesFromGroupData(RuleGroup $ruleGroup, int $offset = 0, ?int $limit = null, bool $ascending = false): array
    {
        $query = $this->getRuleSelectQuery();

        $query->andWhere(
            $query->expr()->eq('rule_group_id', ':rule_group_id'),
        )
        ->setParameter('rule_group_id', $ruleGroup->id, Types::INTEGER);

        $query->addOrderBy('rd.priority', $ascending ? 'ASC' : 'DESC');

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
            ->from('nglayouts_rule');

        $query->andWhere(
            $query->expr()->eq('rule_group_id', ':rule_group_id'),
        )
        ->setParameter('rule_group_id', $ruleGroup->id, Types::INTEGER);

        $this->applyStatusCondition($query, $ruleGroup->status);

        $data = $query->execute()->fetchAllAssociative();

        return (int) ($data[0]['count'] ?? 0);
    }

    /**
     * Returns all rule data for rules from the provided group that match specified target type and value.
     *
     * @param mixed $targetValue
     *
     * @return mixed[]
     */
    public function matchRules(RuleGroup $ruleGroup, string $targetType, $targetValue): array
    {
        $query = $this->getRuleSelectQuery();
        $query
            ->innerJoin(
                'r',
                'nglayouts_rule_target',
                'rt',
                $query->expr()->and(
                    $query->expr()->eq('r.id', 'rt.rule_id'),
                    $query->expr()->eq('r.status', 'rt.status'),
                ),
            )
            ->where(
                $query->expr()->and(
                    $query->expr()->eq('r.rule_group_id', ':rule_group_id'),
                    $query->expr()->eq('rd.enabled', ':enabled'),
                    $query->expr()->eq('rt.type', ':target_type'),
                ),
            )
            ->setParameter('rule_group_id', $ruleGroup->id, Types::INTEGER)
            ->setParameter('target_type', $targetType, Types::STRING)
            ->setParameter('enabled', true, Types::BOOLEAN)
            ->addOrderBy('rd.priority', 'DESC');

        $this->applyStatusCondition($query, Value::STATUS_PUBLISHED, 'r.status');

        $targetHandler = $this->getTargetHandler($targetType);
        $targetHandler->handleQuery($query, $targetValue);

        return $query->execute()->fetchAllAssociative();
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

        $ruleGroupsData = $query->execute()->fetchAllAssociative();

        // Inject the parent UUID into the result
        // This is to avoid inner joining the block table with itself

        if (count($ruleGroupsData) > 0 && $ruleGroupsData[0]['parent_id'] > 0) {
            $parentUuid = $this->getRuleGroupUuid((int) $ruleGroupsData[0]['parent_id']);
            if ($parentUuid === null) {
                // Having a parent ID, but not being able to find the UUID should not happen.
                // If it does, return any empty array as if the rule group with provided ID and status
                // does not exist.
                return [];
            }

            foreach ($ruleGroupsData as &$ruleGroupData) {
                $ruleGroupData['parent_uuid'] = $parentUuid;
            }
        }

        return $ruleGroupsData;
    }

    /**
     * Returns all data for all rule groups from the provided rule group.
     *
     * @return mixed[]
     */
    public function loadRuleGroupsData(RuleGroup $ruleGroup, int $offset = 0, ?int $limit = null, bool $ascending = false): array
    {
        $query = $this->getRuleGroupSelectQuery();

        $query->andWhere(
            $query->expr()->eq('parent_id', ':parent_id'),
        )
        ->setParameter('parent_id', $ruleGroup->id, Types::INTEGER);

        $query->addOrderBy('rgd.priority', $ascending ? 'ASC' : 'DESC');

        $this->applyStatusCondition($query, $ruleGroup->status, 'rg.status');
        $this->applyOffsetAndLimit($query, $offset, $limit);

        $ruleGroupsData = $query->execute()->fetchAllAssociative();

        foreach ($ruleGroupsData as &$ruleGroupData) {
            $ruleGroupData['parent_uuid'] = $ruleGroup->uuid;
        }

        return $ruleGroupsData;
    }

    /**
     * Returns the number of rule groups.
     */
    public function getRuleGroupCount(RuleGroup $ruleGroup): int
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('nglayouts_rule_group');

        $query->andWhere(
            $query->expr()->eq('parent_id', ':parent_id'),
        )
        ->setParameter('parent_id', $ruleGroup->id, Types::INTEGER);

        $this->applyStatusCondition($query, $ruleGroup->status);

        $data = $query->execute()->fetchAllAssociative();

        return (int) ($data[0]['count'] ?? 0);
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
            $query->expr()->eq('t.rule_id', ':rule_id'),
        )
        ->setParameter('rule_id', $rule->id, Types::INTEGER)
        ->orderBy('t.id', 'ASC');

        $this->applyStatusCondition($query, $rule->status, 't.status');

        return $query->execute()->fetchAllAssociative();
    }

    /**
     * Returns the number of targets within the rule.
     */
    public function getRuleTargetCount(Rule $rule): int
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('nglayouts_rule_target')
            ->where(
                $query->expr()->eq('rule_id', ':rule_id'),
            )
            ->setParameter('rule_id', $rule->id, Types::INTEGER);

        $this->applyStatusCondition($query, $rule->status);

        $data = $query->execute()->fetchAllAssociative();

        return (int) ($data[0]['count'] ?? 0);
    }

    /**
     * Returns all data for specified rule condition.
     *
     * @param int|string $conditionId
     *
     * @return mixed[]
     */
    public function loadRuleConditionData($conditionId, int $status): array
    {
        $query = $this->getRuleConditionSelectQuery();

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
        $query = $this->getRuleConditionSelectQuery();
        $query->where(
            $query->expr()->eq('cr.rule_id', ':rule_id'),
        )
        ->setParameter('rule_id', $rule->id, Types::INTEGER)
        ->orderBy('c.id', 'ASC');

        $this->applyStatusCondition($query, $rule->status, 'c.status');

        return $query->execute()->fetchAllAssociative();
    }

    /**
     * Returns all data for specified rule group condition.
     *
     * @param int|string $conditionId
     *
     * @return mixed[]
     */
    public function loadRuleGroupConditionData($conditionId, int $status): array
    {
        $query = $this->getRuleGroupConditionSelectQuery();

        $this->applyIdCondition($query, $conditionId, 'c.id', 'c.uuid');
        $this->applyStatusCondition($query, $status, 'c.status');

        return $query->execute()->fetchAllAssociative();
    }

    /**
     * Returns all data for for all rule group conditions.
     *
     * @return mixed[]
     */
    public function loadRuleGroupConditionsData(RuleGroup $ruleGroup): array
    {
        $query = $this->getRuleGroupConditionSelectQuery();
        $query->where(
            $query->expr()->eq('crg.rule_group_id', ':rule_group_id'),
        )
        ->setParameter('rule_group_id', $ruleGroup->id, Types::INTEGER)
        ->orderBy('c.id', 'ASC');

        $this->applyStatusCondition($query, $ruleGroup->status, 'c.status');

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
     * Returns the lowest priority from the list of all the rules and rule groups in provided rule group.
     */
    public function getLowestPriority(RuleGroup $parentGroup): ?int
    {
        // Get the lowest priority for rules

        $query = $this->connection->createQueryBuilder();
        $query->select('rd.priority')
            ->from('nglayouts_rule_data', 'rd')
            ->innerJoin(
                'rd',
                'nglayouts_rule',
                'r',
                $query->expr()->eq('rd.rule_id', 'r.id'),
            )
            ->where(
                $query->expr()->eq('r.rule_group_id', ':rule_group_id'),
            )
            ->setParameter('rule_group_id', $parentGroup->id, Types::INTEGER);

        $query->addOrderBy('rd.priority', 'ASC');
        $this->applyOffsetAndLimit($query, 0, 1);

        $data = $query->execute()->fetchAllAssociative();

        // Get the lowest priority for rule groups

        $lowestRulePriority = isset($data[0]['priority']) ? (int) $data[0]['priority'] : null;

        $query = $this->connection->createQueryBuilder();
        $query->select('rgd.priority')
            ->from('nglayouts_rule_group_data', 'rgd')
            ->innerJoin(
                'rgd',
                'nglayouts_rule_group',
                'rg',
                $query->expr()->eq('rgd.rule_group_id', 'rg.id'),
            )
            ->where(
                $query->expr()->eq('rg.parent_id', ':parent_id'),
            )
            ->setParameter('parent_id', $parentGroup->id, Types::INTEGER);

        $query->addOrderBy('rgd.priority', 'ASC');
        $this->applyOffsetAndLimit($query, 0, 1);

        $data = $query->execute()->fetchAllAssociative();

        // Return the lowest priority between lowest rule and lowest rule group priority

        $lowestRuleGroupPriority = isset($data[0]['priority']) ? (int) $data[0]['priority'] : null;

        if ($lowestRulePriority !== null && $lowestRuleGroupPriority !== null) {
            return min($lowestRulePriority, $lowestRuleGroupPriority);
        }

        return $lowestRuleGroupPriority ?? $lowestRulePriority;
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
                    'rule_group_id' => ':rule_group_id',
                    'layout_uuid' => ':layout_uuid',
                    'description' => ':description',
                ],
            )
            ->setValue('id', $rule->id ?? $this->connectionHelper->nextId('nglayouts_rule'))
            ->setParameter('uuid', $rule->uuid, Types::STRING)
            ->setParameter('status', $rule->status, Types::INTEGER)
            ->setParameter('rule_group_id', $rule->ruleGroupId, Types::INTEGER)
            ->setParameter('layout_uuid', $rule->layoutUuid, Types::STRING)
            ->setParameter('description', $rule->description, Types::STRING);

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
                    ],
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
            ->set('description', ':description')
            ->where(
                $query->expr()->eq('id', ':id'),
            )
            ->setParameter('id', $rule->id, Types::INTEGER)
            ->setParameter('uuid', $rule->uuid, Types::STRING)
            ->setParameter('layout_uuid', $rule->layoutUuid, Types::STRING)
            ->setParameter('description', $rule->description, Types::STRING);

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
                $query->expr()->eq('rule_id', ':rule_id'),
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
            ->set('rule_group_id', ':rule_group_id')
            ->where(
                $query->expr()->eq('id', ':id'),
            )
            ->setParameter('id', $rule->id, Types::INTEGER)
            ->setParameter('rule_group_id', $targetGroup->id, Types::INTEGER);

        $this->applyStatusCondition($query, $rule->status);

        $query->execute();

        if ($newPriority !== null) {
            $query = $this->connection->createQueryBuilder();
            $query
                ->update('nglayouts_rule_data')
                ->set('priority', ':priority')
                ->where(
                    $query->expr()->eq('rule_id', ':rule_id'),
                )
                ->setParameter('rule_id', $rule->id, Types::INTEGER)
                ->setParameter('priority', $newPriority, Types::INTEGER);

            $query->execute();
        }
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
                    'name' => ':name',
                    'description' => ':description',
                ],
            )
            ->setValue('id', $ruleGroup->id ?? $this->connectionHelper->nextId('nglayouts_rule_group'))
            ->setParameter('uuid', $ruleGroup->uuid, Types::STRING)
            ->setParameter('status', $ruleGroup->status, Types::INTEGER)
            ->setParameter('depth', $ruleGroup->depth, Types::INTEGER)
            // Materialized path is updated after rule group is created
            ->setParameter('path', $ruleGroup->path, Types::STRING)
            ->setParameter('parent_id', $ruleGroup->parentId, Types::INTEGER)
            ->setParameter('name', $ruleGroup->name, Types::STRING)
            ->setParameter('description', $ruleGroup->description, Types::STRING);

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
                    ],
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
                $query->expr()->eq('id', ':id'),
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
            ->set('name', ':name')
            ->set('description', ':description')
            ->where(
                $query->expr()->eq('id', ':id'),
            )
            ->setParameter('id', $ruleGroup->id, Types::INTEGER)
            ->setParameter('uuid', $ruleGroup->uuid, Types::STRING)
            ->setParameter('depth', $ruleGroup->depth, Types::INTEGER)
            ->setParameter('path', $ruleGroup->path, Types::STRING)
            ->setParameter('parent_id', $ruleGroup->parentId, Types::INTEGER)
            ->setParameter('name', $ruleGroup->name, Types::STRING)
            ->setParameter('description', $ruleGroup->description, Types::STRING);

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
                $query->expr()->eq('rule_group_id', ':rule_group_id'),
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
                $query->expr()->eq('id', ':id'),
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
                $query->expr()->like('path', ':path'),
            )
            ->setParameter('depth_difference', $depthDifference, Types::INTEGER)
            ->setParameter('old_path', $ruleGroup->path, Types::STRING)
            ->setParameter('new_path', $targetGroup->path . $ruleGroup->id . '/', Types::STRING)
            ->setParameter('path', $ruleGroup->path . '%', Types::STRING);

        $this->applyStatusCondition($query, $ruleGroup->status);

        $query->execute();

        if ($newPriority !== null) {
            $query = $this->connection->createQueryBuilder();
            $query
                ->update('nglayouts_rule_group_data')
                ->set('priority', ':priority')
                ->where(
                    $query->expr()->eq('rule_group_id', ':rule_group_id'),
                )
                ->setParameter('rule_group_id', $ruleGroup->id, Types::INTEGER)
                ->setParameter('priority', $newPriority, Types::INTEGER);

            $query->execute();
        }
    }

    /**
     * Deletes all rule targets for provided rule IDs.
     *
     * @param int[] $ruleIds
     */
    public function deleteRuleTargets(array $ruleIds, ?int $status = null): void
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->delete('nglayouts_rule_target')
            ->where(
                $query->expr()->in('rule_id', [':rule_id']),
            )
            ->setParameter('rule_id', $ruleIds, Connection::PARAM_INT_ARRAY);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $query->execute();
    }

    /**
     * Delete all rule conditions for provided rule IDs.
     *
     * @param int[] $ruleIds
     */
    public function deleteRuleConditions(array $ruleIds, ?int $status = null): void
    {
        $conditionIds = $this->loadRuleConditionIds($ruleIds);

        // Delete the connections between conditions and rules

        $query = $this->connection->createQueryBuilder();
        $query
            ->delete('nglayouts_rule_condition_rule')
            ->where(
                $query->expr()->in('condition_id', [':condition_id']),
            )
            ->setParameter('condition_id', $conditionIds, Connection::PARAM_INT_ARRAY);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status, 'condition_status');
        }

        $query->execute();

        // Delete the conditions themselves

        $query = $this->connection->createQueryBuilder();
        $query
            ->delete('nglayouts_rule_condition')
            ->where(
                $query->expr()->in('id', [':id']),
            )
            ->setParameter('id', $conditionIds, Connection::PARAM_INT_ARRAY);

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
                $query->expr()->eq('id', ':id'),
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
                    $query->expr()->eq('rule_id', ':rule_id'),
                )
                ->setParameter('rule_id', $ruleId, Types::INTEGER);

            $query->execute();
        }
    }

    /**
     * Deletes all rules with provided IDs.
     *
     * @param int[] $ruleIds
     */
    public function deleteRules(array $ruleIds): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('nglayouts_rule')
            ->where(
                $query->expr()->in('id', [':id']),
            )
            ->setParameter('id', $ruleIds, Connection::PARAM_INT_ARRAY);

        $query->execute();

        $query->delete('nglayouts_rule_data')
            ->where(
                $query->expr()->in('rule_id', [':rule_id']),
            )
            ->setParameter('rule_id', $ruleIds, Connection::PARAM_INT_ARRAY);

        $query->execute();
    }

    /**
     * Loads all sub group IDs for the provided group ID.
     *
     * @return int[]
     */
    public function loadSubGroupIds(int $ruleGroupId): array
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT id')
            ->from('nglayouts_rule_group')
            ->where(
                $query->expr()->like('path', ':path'),
            )
            ->setParameter('path', '%/' . $ruleGroupId . '/%', Types::STRING);

        $result = $query->execute()->fetchAllAssociative();

        return array_map('intval', array_column($result, 'id'));
    }

    /**
     * Loads all sub rule IDs for the provided group IDs.
     *
     * @param int[] $ruleGroupIds
     *
     * @return int[]
     */
    public function loadSubRuleIds(array $ruleGroupIds): array
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT id')
            ->from('nglayouts_rule')
            ->where(
                $query->expr()->in('rule_group_id', [':rule_group_id']),
            )
            ->setParameter('rule_group_id', $ruleGroupIds, Connection::PARAM_INT_ARRAY);

        $result = $query->execute()->fetchAllAssociative();

        return array_map('intval', array_column($result, 'id'));
    }

    /**
     * Delete all rule group conditions for provided rule group IDs.
     *
     * @param int[] $ruleGroupIds
     */
    public function deleteRuleGroupConditions(array $ruleGroupIds, ?int $status = null): void
    {
        $conditionIds = $this->loadRuleGroupConditionIds($ruleGroupIds);

        // Delete the connections between conditions and rule groups

        $query = $this->connection->createQueryBuilder();
        $query
            ->delete('nglayouts_rule_condition_rule_group')
            ->where(
                $query->expr()->in('condition_id', [':condition_id']),
            )
            ->setParameter('condition_id', $conditionIds, Connection::PARAM_INT_ARRAY);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status, 'condition_status');
        }

        $query->execute();

        // Delete the conditions themselves

        $query = $this->connection->createQueryBuilder();
        $query
            ->delete('nglayouts_rule_condition')
            ->where(
                $query->expr()->in('id', [':id']),
            )
            ->setParameter('id', $conditionIds, Connection::PARAM_INT_ARRAY);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $query->execute();
    }

    /**
     * Deletes a rule group.
     */
    public function deleteRuleGroup(int $ruleGroupId, ?int $status = null): void
    {
        $query = $this->connection->createQueryBuilder();
        $query->delete('nglayouts_rule_group')
            ->where(
                $query->expr()->eq('id', ':id'),
            )
            ->setParameter('id', $ruleGroupId, Types::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $query->execute();

        if (!$this->ruleGroupExists($ruleGroupId)) {
            $query = $this->connection->createQueryBuilder();
            $query->delete('nglayouts_rule_group_data')
                ->where(
                    $query->expr()->eq('rule_group_id', ':rule_group_id'),
                )
                ->setParameter('rule_group_id', $ruleGroupId, Types::INTEGER);

            $query->execute();
        }
    }

    /**
     * Deletes all rule groups with provided IDs.
     *
     * @param int[] $ruleGroupIds
     */
    public function deleteRuleGroups(array $ruleGroupIds): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('nglayouts_rule_group')
            ->where(
                $query->expr()->in('id', [':id']),
            )
            ->setParameter('id', $ruleGroupIds, Connection::PARAM_INT_ARRAY);

        $query->execute();

        $query = $this->connection->createQueryBuilder();

        $query->delete('nglayouts_rule_group_data')
            ->where(
                $query->expr()->in('rule_group_id', [':rule_group_id']),
            )
            ->setParameter('rule_group_id', $ruleGroupIds, Connection::PARAM_INT_ARRAY);

        $query->execute();
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
                ],
            )
            ->setValue('id', $target->id ?? $this->connectionHelper->nextId('nglayouts_rule_target'))
            ->setParameter('uuid', $target->uuid, Types::STRING)
            ->setParameter('status', $target->status, Types::INTEGER)
            ->setParameter('rule_id', $target->ruleId, Types::INTEGER)
            ->setParameter('type', $target->type, Types::STRING)
            ->setParameter('value', $target->value, is_array($target->value) ? Types::JSON : Types::STRING);

        $query->execute();

        $target->id ??= (int) $this->connectionHelper->lastId('nglayouts_rule_target');

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
                $query->expr()->eq('id', ':id'),
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
                $query->expr()->eq('id', ':id'),
            )
            ->setParameter('id', $targetId, Types::INTEGER);

        $this->applyStatusCondition($query, $status);

        $query->execute();
    }

    /**
     * Adds a rule condition.
     */
    public function addRuleCondition(RuleCondition $condition): RuleCondition
    {
        /** @var \Netgen\Layouts\Persistence\Values\LayoutResolver\RuleCondition $ruleCondition */
        $ruleCondition = $this->addCondition($condition);

        $query = $this->connection->createQueryBuilder()
            ->insert('nglayouts_rule_condition_rule')
            ->values(
                [
                    'condition_id' => ':condition_id',
                    'condition_status' => ':condition_status',
                    'rule_id' => ':rule_id',
                    'rule_status' => ':rule_status',
                ],
            )
            ->setParameter('condition_id', $ruleCondition->id, Types::INTEGER)
            ->setParameter('condition_status', $ruleCondition->status, Types::INTEGER)
            ->setParameter('rule_id', $ruleCondition->ruleId, Types::INTEGER)
            ->setParameter('rule_status', $ruleCondition->status, Types::INTEGER);

        $query->execute();

        return $ruleCondition;
    }

    /**
     * Adds a rule group condition.
     */
    public function addRuleGroupCondition(RuleGroupCondition $condition): RuleGroupCondition
    {
        /** @var \Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroupCondition $ruleGroupCondition */
        $ruleGroupCondition = $this->addCondition($condition);

        $query = $this->connection->createQueryBuilder()
            ->insert('nglayouts_rule_condition_rule_group')
            ->values(
                [
                    'condition_id' => ':condition_id',
                    'condition_status' => ':condition_status',
                    'rule_group_id' => ':rule_group_id',
                    'rule_group_status' => ':rule_group_status',
                ],
            )
            ->setParameter('condition_id', $ruleGroupCondition->id, Types::INTEGER)
            ->setParameter('condition_status', $ruleGroupCondition->status, Types::INTEGER)
            ->setParameter('rule_group_id', $ruleGroupCondition->ruleGroupId, Types::INTEGER)
            ->setParameter('rule_group_status', $ruleGroupCondition->status, Types::INTEGER);

        $query->execute();

        return $ruleGroupCondition;
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
            ->set('type', ':type')
            ->set('value', ':value')
            ->where(
                $query->expr()->eq('id', ':id'),
            )
            ->setParameter('id', $condition->id, Types::INTEGER)
            ->setParameter('uuid', $condition->uuid, Types::STRING)
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
        // Delete connection between condition and rule

        $query = $this->connection->createQueryBuilder();

        $query->delete('nglayouts_rule_condition_rule')
            ->where(
                $query->expr()->eq('condition_id', ':condition_id'),
            )
            ->setParameter('condition_id', $conditionId, Types::INTEGER);

        $this->applyStatusCondition($query, $status, 'condition_status');

        $query->execute();

        // Delete connection between condition and rule group

        $query = $this->connection->createQueryBuilder();

        $query->delete('nglayouts_rule_condition_rule_group')
            ->where(
                $query->expr()->eq('condition_id', ':condition_id'),
            )
            ->setParameter('condition_id', $conditionId, Types::INTEGER);

        $this->applyStatusCondition($query, $status, 'condition_status');

        $query->execute();

        // Delete the condition itself

        $query = $this->connection->createQueryBuilder();

        $query->delete('nglayouts_rule_condition')
            ->where(
                $query->expr()->eq('id', ':id'),
            )
            ->setParameter('id', $conditionId, Types::INTEGER);

        $this->applyStatusCondition($query, $status);

        $query->execute();
    }

    /**
     * Adds a condition.
     */
    private function addCondition(Condition $condition): Condition
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('nglayouts_rule_condition')
            ->values(
                [
                    'id' => ':id',
                    'uuid' => ':uuid',
                    'status' => ':status',
                    'type' => ':type',
                    'value' => ':value',
                ],
            )
            ->setValue('id', $condition->id ?? $this->connectionHelper->nextId('nglayouts_rule_condition'))
            ->setParameter('uuid', $condition->uuid, Types::STRING)
            ->setParameter('status', $condition->status, Types::INTEGER)
            ->setParameter('type', $condition->type, Types::STRING)
            ->setParameter('value', json_encode($condition->value), Types::STRING);

        $query->execute();

        $condition->id ??= (int) $this->connectionHelper->lastId('nglayouts_rule_condition');

        return $condition;
    }

    /**
     * Returns all condition IDs belonging to provided rule IDs.
     *
     * @param int[] $ruleIds
     *
     * @return int[]
     */
    private function loadRuleConditionIds(array $ruleIds): array
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT condition_id')
            ->from('nglayouts_rule_condition_rule')
            ->where(
                $query->expr()->in('rule_id', [':rule_id']),
            )
            ->setParameter('rule_id', $ruleIds, Connection::PARAM_INT_ARRAY);

        $result = $query->execute()->fetchAllAssociative();

        return array_map('intval', array_column($result, 'condition_id'));
    }

    /**
     * Returns all condition IDs belonging to provided rule group IDs.
     *
     * @param int[] $ruleGroupIds
     *
     * @return int[]
     */
    private function loadRuleGroupConditionIds(array $ruleGroupIds): array
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT condition_id')
            ->from('nglayouts_rule_condition_rule_group')
            ->where(
                $query->expr()->in('rule_group_id', [':rule_group_id']),
            )
            ->setParameter('rule_group_id', $ruleGroupIds, Connection::PARAM_INT_ARRAY);

        $result = $query->execute()->fetchAllAssociative();

        return array_map('intval', array_column($result, 'condition_id'));
    }

    /**
     * Returns the rule group UUID for provided rule group ID.
     *
     * If rule group with provided ID does not exist, null is returned.
     */
    private function getRuleGroupUuid(int $ruleGroupId): ?string
    {
        $query = $this->connection->createQueryBuilder();

        $query->select('rg.uuid')
            ->from('nglayouts_rule_group', 'rg')
            ->where(
                $query->expr()->eq('rg.id', ':id'),
            )
            ->setParameter('id', $ruleGroupId, Types::INTEGER);

        $this->applyOffsetAndLimit($query, 0, 1);

        $data = $query->execute()->fetchAllAssociative();

        if (count($data) === 0) {
            return null;
        }

        return $data[0]['uuid'];
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
                $query->expr()->eq('rd.rule_id', 'r.id'),
            )->leftJoin(
                'r',
                'nglayouts_layout',
                'l',
                $query->expr()->and(
                    $query->expr()->eq('r.layout_uuid', 'l.uuid'),
                    $query->expr()->eq('l.status', Value::STATUS_PUBLISHED),
                ),
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
                $query->expr()->eq('rgd.rule_group_id', 'rg.id'),
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
                    $query->expr()->eq('r.status', 't.status'),
                ),
            );

        return $query;
    }

    /**
     * Builds and returns a rule condition database SELECT query.
     */
    private function getRuleConditionSelectQuery(): QueryBuilder
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT c.*, cr.rule_id, r.uuid AS rule_uuid')
            ->from('nglayouts_rule_condition', 'c')
            ->innerJoin(
                'c',
                'nglayouts_rule_condition_rule',
                'cr',
                $query->expr()->and(
                    $query->expr()->eq('cr.condition_id', 'c.id'),
                    $query->expr()->eq('cr.condition_status', 'c.status'),
                ),
            )
            ->innerJoin(
                'cr',
                'nglayouts_rule',
                'r',
                $query->expr()->and(
                    $query->expr()->eq('r.id', 'cr.rule_id'),
                    $query->expr()->eq('r.status', 'cr.rule_status'),
                ),
            );

        return $query;
    }

    /**
     * Builds and returns a rule group condition database SELECT query.
     */
    private function getRuleGroupConditionSelectQuery(): QueryBuilder
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT c.*, crg.rule_group_id, rg.uuid AS rule_group_uuid')
            ->from('nglayouts_rule_condition', 'c')
            ->innerJoin(
                'c',
                'nglayouts_rule_condition_rule_group',
                'crg',
                $query->expr()->and(
                    $query->expr()->eq('crg.condition_id', 'c.id'),
                    $query->expr()->eq('crg.condition_status', 'c.status'),
                ),
            )
            ->innerJoin(
                'crg',
                'nglayouts_rule_group',
                'rg',
                $query->expr()->and(
                    $query->expr()->eq('rg.id', 'crg.rule_group_id'),
                    $query->expr()->eq('rg.status', 'crg.rule_group_status'),
                ),
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
