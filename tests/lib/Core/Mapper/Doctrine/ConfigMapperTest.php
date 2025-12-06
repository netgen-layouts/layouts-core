<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Mapper\Doctrine;

use Netgen\Layouts\Core\Mapper\ConfigMapper;
use Netgen\Layouts\Tests\Core\Mapper\ConfigMapperTestBase;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ConfigMapper::class)]
final class ConfigMapperTest extends ConfigMapperTestBase
{
    use TestCaseTrait;
}
