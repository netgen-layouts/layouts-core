<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine\Stubs;

use Netgen\Layouts\Persistence\Doctrine\QueryHandler\QueryHandler;

final class EmptyQueryHandler extends QueryHandler
{
    public function __construct() {}
}
