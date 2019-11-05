<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\QueryHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Types;
use Netgen\Layouts\Exception\Persistence\TargetHandlerException;
use Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper;
use Netgen\Layouts\Persistence\Values\Layout\Layout;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Condition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Rule;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Target;
use Netgen\Layouts\Persistence\Values\Value;
use PDO;
use Psr\Container\ContainerInterface;

final class LayoutResolverQueryHandler extends QueryHandler
{
    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $targetHandlers;

    public function __construct(Connection $connection, ConnectionHelper $connectionHelper, ContainerInterface $targetHandlers)
    {
        $this->targetHandlers = $targetHandlers;

        parent::__construct($connection, $connectionHelper);
    }

    /**
     * Returns all data for specified rule.
     *
     * @param int|string $ruleId
     * @param int $status
     *
     * @return array
     */
    public function loadRuleData($ruleId, int $status): array
    {
        $query = $this->getRuleSelectQuery();

        $this->applyIdCondition($query, $ruleId, 'r.id', 'r.uuid');
        $this->applyStatusCondition($query, $status, 'r.status');

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns all data for all rules.
     */
    public function loadRulesData(int $status, ?Layout $layout = null, int $offset = 0, ?int $limit = null): array
    {
        $query = $this->getRuleSelectQuery();

        if ($layout instanceof Layout) {
            $query->andWhere(
                $query->expr()->eq('layout_id', ':layout_id')
            )
            ->setParameter('layout_id', $layout->id, Types::INTEGER);
        }

        $query->addOrderBy('rd.priority', 'DESC');

        $this->applyStatusCondition($query, $status, 'r.status');
        $this->applyOffsetAndLimit($query, $offset, $limit);

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
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
                $query->expr()->eq('layout_id', ':layout_id')
            )
            ->setParameter('layout_id', $layout->id, Types::INTEGER);
        }

        $this->applyStatusCondition($query, $ruleStatus);

        $data = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

        return (int) ($data[0]['count'] ?? 0);
    }

    /**
     * Returns all rule data for rules that match specified target type and value.
     *
     * @param string $targetType
     * @param mixed $targetValue
     *
     * @return array
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

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns all data for specified target.
     *
     * @param int|string $targetId
     * @param int $status
     *
     * @return array
     */
    public function loadTargetData($targetId, int $status): array
    {
        $query = $this->getTargetSelectQuery();

        $this->applyIdCondition($query, $targetId, 't.id', 't.uuid');
        $this->applyStatusCondition($query, $status, 't.status');

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns all data for all rule targets.
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

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
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

        $data = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

        return (int) ($data[0]['count'] ?? 0);
    }

    /**
     * Returns all data for specified condition.
     *
     * @param int|string $conditionId
     * @param int $status
     *
     * @return array
     */
    public function loadConditionData($conditionId, int $status): array
    {
        $query = $this->getConditionSelectQuery();

        $this->applyIdCondition($query, $conditionId, 'c.id', 'c.uuid');
        $this->applyStatusCondition($query, $status, 'c.status');

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns all data for for all rule conditions.
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

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns if the specified rule exists.
     *
     * @param int|string $ruleId
     * @param int $status
     *
     * @return bool
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

        $data = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

        return (int) ($data[0]['count'] ?? 0) > 0;
    }

    /**
     * Returns the lowest priority from the list of all the rules.
     */
    public function getLowestRulePriority(): ?int
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('priority')
            ->from('nglayouts_rule_data');

        $query->addOrderBy('priority', 'ASC');
        $this->applyOffsetAndLimit($query, 0, 1);

        $data = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

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
                    'layout_id' => ':layout_id',
                    'comment' => ':comment',
                ]
            )
            ->setValue('id', $rule->id ?? $this->connectionHelper->getAutoIncrementValue('nglayouts_rule'))
            ->setParameter('uuid', $rule->uuid, Types::STRING)
            ->setParameter('status', $rule->status, Types::INTEGER)
            ->setParameter('layout_id', $rule->layoutId, Types::INTEGER)
            ->setParameter('comment', $rule->comment, Types::STRING);

        $query->execute();

        if ($rule->id === null) {
            $rule->id = (int) $this->connectionHelper->lastInsertId('nglayouts_rule');

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
            ->set('layout_id', ':layout_id')
            ->set('comment', ':comment')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $rule->id, Types::INTEGER)
            ->setParameter('uuid', $rule->uuid, Types::STRING)
            ->setParameter('layout_id', $rule->layoutId, Types::INTEGER)
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
            ->setValue('id', $target->id ?? $this->connectionHelper->getAutoIncrementValue('nglayouts_rule_target'))
            ->setParameter('uuid', $target->uuid, Types::STRING)
            ->setParameter('status', $target->status, Types::INTEGER)
            ->setParameter('rule_id', $target->ruleId, Types::INTEGER)
            ->setParameter('type', $target->type, Types::STRING)
            ->setParameter('value', $target->value, is_array($target->value) ? Types::JSON : Types::STRING);

        $query->execute();

        $target->id = $target->id ?? (int) $this->connectionHelper->lastInsertId('nglayouts_rule_target');

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
            ->setValue('id', $condition->id ?? $this->connectionHelper->getAutoIncrementValue('nglayouts_rule_condition'))
            ->setParameter('uuid', $condition->uuid, Types::STRING)
            ->setParameter('status', $condition->status, Types::INTEGER)
            ->setParameter('rule_id', $condition->ruleId, Types::INTEGER)
            ->setParameter('type', $condition->type, Types::STRING)
            ->setParameter('value', json_encode($condition->value), Types::STRING);

        $query->execute();

        $condition->id = $condition->id ?? (int) $this->connectionHelper->lastInsertId('nglayouts_rule_condition');

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
                $query->expr()->andX(
                    $query->expr()->eq('r.layout_id', 'l.id'),
                    $query->expr()->eq('l.status', Value::STATUS_PUBLISHED)
                )
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
                $query->expr()->andX(
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
                $query->expr()->andX(
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
