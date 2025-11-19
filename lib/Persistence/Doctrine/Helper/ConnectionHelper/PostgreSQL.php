<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper;

use Doctrine\DBAL\Connection;
use Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelperInterface;

use function sprintf;

final class PostgreSQL implements ConnectionHelperInterface
{
    public function __construct(
        private Connection $connection,
    ) {}

    public function nextId(string $table, string $column = 'id'): string
    {
        return sprintf("nextval('%s_%s_seq')", $table, $column);
    }

    public function lastId(string $table, string $column = 'id'): int
    {
        $query = $this->connection->createQueryBuilder();
        $query->select(sprintf("currval('%s_%s_seq') as currval", $table, $column));

        $data = $query->fetchAllAssociative();

        return (int) ($data[0]['currval'] ?? 0);
    }
}
