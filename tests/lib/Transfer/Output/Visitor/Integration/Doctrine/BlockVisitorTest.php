<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\BlockVisitorTest as BaseBlockVisitorTest;

/**
 * @covers \Netgen\Layouts\Transfer\Output\Visitor\BlockVisitor
 */
final class BlockVisitorTest extends BaseBlockVisitorTest
{
    use TestCaseTrait;

    public function tearDown(): void
    {
        $this->closeDatabase();
    }
}
