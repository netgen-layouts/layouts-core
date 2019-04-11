<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\CollectionVisitorTest as BaseCollectionVisitorTest;

/**
 * @covers \Netgen\Layouts\Transfer\Output\Visitor\CollectionVisitor
 */
final class CollectionVisitorTest extends BaseCollectionVisitorTest
{
    use TestCaseTrait;

    public function tearDown(): void
    {
        $this->closeDatabase();
    }
}
