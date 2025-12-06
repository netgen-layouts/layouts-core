<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Mapper\Doctrine;

use Netgen\Layouts\Core\Mapper\LayoutResolverMapper;
use Netgen\Layouts\Tests\Core\Mapper\LayoutResolverMapperTestBase;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LayoutResolverMapper::class)]
final class LayoutResolverMapperTest extends LayoutResolverMapperTestBase
{
    use TestCaseTrait;
}
