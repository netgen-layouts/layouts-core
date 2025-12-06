<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Mapper\Doctrine;

use Netgen\Layouts\Core\Mapper\CollectionMapper;
use Netgen\Layouts\Tests\Core\Mapper\CollectionMapperTestBase;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CollectionMapper::class)]
final class CollectionMapperTest extends CollectionMapperTestBase
{
    use TestCaseTrait;
}
