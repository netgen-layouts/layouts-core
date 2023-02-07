<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\CollectionVisitorTestBase;

/**
 * @covers \Netgen\Layouts\Transfer\Output\Visitor\CollectionVisitor
 */
final class CollectionVisitorTest extends CollectionVisitorTestBase
{
    use TestCaseTrait;

    protected function tearDown(): void
    {
        $this->closeDatabase();
    }
}
