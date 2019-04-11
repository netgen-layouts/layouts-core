<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\StructBuilder\Doctrine;

use Netgen\Layouts\Tests\Core\StructBuilder\CollectionStructBuilderTest as BaseCollectionStructBuilderTest;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;

final class CollectionStructBuilderTest extends BaseCollectionStructBuilderTest
{
    use TestCaseTrait;

    public function tearDown(): void
    {
        $this->closeDatabase();
    }
}
