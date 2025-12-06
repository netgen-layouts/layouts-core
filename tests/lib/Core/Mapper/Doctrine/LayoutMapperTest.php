<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Mapper\Doctrine;

use Netgen\Layouts\Core\Mapper\LayoutMapper;
use Netgen\Layouts\Tests\Core\Mapper\LayoutMapperTestBase;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LayoutMapper::class)]
final class LayoutMapperTest extends LayoutMapperTestBase
{
    use TestCaseTrait;
}
