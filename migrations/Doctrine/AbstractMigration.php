<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Migrations\Doctrine;

use Doctrine\DBAL\Migrations\AbstractMigration as LegacyAbstractMigration;
use Doctrine\Migrations\AbstractMigration as BaseAbstractMigration;

if (!class_exists(BaseAbstractMigration::class)) {
    class_alias(LegacyAbstractMigration::class, BaseAbstractMigration::class);
}

abstract class AbstractMigration extends BaseAbstractMigration
{
}
