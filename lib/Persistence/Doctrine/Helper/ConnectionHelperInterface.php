<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\Helper;

interface ConnectionHelperInterface
{
    /**
     * Returns the auto increment value.
     *
     * Returns the value used for autoincrement tables. Usually this will just
     * be null. In case for sequence based RDBMS, this method can return a
     * proper value for the given column.
     *
     * @return mixed
     */
    public function nextId(string $table, string $column = 'id');

    /**
     * Returns the last inserted ID.
     *
     * @return mixed
     */
    public function lastId(string $table, string $column = 'id');
}
