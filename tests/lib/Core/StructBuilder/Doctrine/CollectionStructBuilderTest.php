<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\StructBuilder\Doctrine;

use Netgen\Layouts\Core\StructBuilder\CollectionStructBuilder;
use Netgen\Layouts\Tests\Core\StructBuilder\CollectionStructBuilderTestBase;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CollectionStructBuilder::class)]
final class CollectionStructBuilderTest extends CollectionStructBuilderTestBase
{
    use TestCaseTrait;
}
