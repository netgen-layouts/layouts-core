<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\ItemVisitorTest as BaseItemVisitorTest;

/**
 * @covers \Netgen\Layouts\Transfer\Output\Visitor\ItemVisitor
 */
final class ItemVisitorTest extends BaseItemVisitorTest
{
    use TestCaseTrait;

    public function tearDown(): void
    {
        $this->closeDatabase();
    }
}
