<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\StructBuilder\Doctrine;

use Netgen\Layouts\Core\StructBuilder\BlockStructBuilder;
use Netgen\Layouts\Tests\Core\StructBuilder\BlockStructBuilderTestBase;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(BlockStructBuilder::class)]
final class BlockStructBuilderTest extends BlockStructBuilderTestBase
{
    use TestCaseTrait;
}
