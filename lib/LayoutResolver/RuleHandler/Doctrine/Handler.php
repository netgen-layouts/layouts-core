<?php

namespace Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine;

use Netgen\BlockManager\LayoutResolver\RuleHandler\RuleHandlerInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;

class Handler implements RuleHandlerInterface
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * @var \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Normalizer
     */
    protected $normalizer;

    /**
     * @var \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler[]
     */
    protected $targetHandlers;

    /**
     * Constructor.
     *
     * @param \Doctrine\DBAL\Connection $connection
     * @param \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Normalizer $normalizer
     */
    public function __construct(Connection $connection, Normalizer $normalizer)
    {
        $this->connection = $connection;
        $this->normalizer = $normalizer;
    }

    /**
     * Adds a target handler to the rule handler.
     *
     * @param string $targetIdentifier
     * @param \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler $targetHandler
     */
    public function addTargetHandler($targetIdentifier, TargetHandler $targetHandler)
    {
        $this->targetHandlers[$targetIdentifier] = $targetHandler;
    }

    /**
     * Loads rules with target identifier and provided values.
     *
     * @param string $targetIdentifier
     * @param array $values
     *
     * @throws \InvalidArgumentException If target handler does not exist for given target identifier
     *
     * @return array
     */
    public function loadRules($targetIdentifier, array $values)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT r.id', 'r.layout_id', 'rc.id AS condition_id', 'rc.identifier', 'rc.parameters')
            ->from('ngbm_rule', 'r')
            ->innerJoin('r', 'ngbm_rule_value', 'rv', 'r.id = rv.rule_id')
            ->leftJoin('r', 'ngbm_rule_condition', 'rc', 'r.id = rc.rule_id')
            ->where(
                $query->expr()->eq('r.target_identifier', ':target_identifier')
            )
            ->setParameter('target_identifier', $targetIdentifier, Type::STRING)
            ->addOrderBy('r.id', 'ASC')
            ->addOrderBy('rc.id', 'ASC');

        if (!isset($this->targetHandlers[$targetIdentifier])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Target handler for "%s" identifier does not exist.',
                    $targetIdentifier
                )
            );
        }

        $this->targetHandlers[$targetIdentifier]->handleQuery($query, $values);

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            return array();
        }

        return $this->normalizer->normalizeRules($data);
    }
}
