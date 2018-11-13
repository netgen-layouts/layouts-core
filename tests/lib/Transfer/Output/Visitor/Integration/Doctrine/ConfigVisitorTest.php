<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration\ConfigVisitorTest as BaseConfigVisitorTest;

/**
 * @covers \Netgen\BlockManager\Transfer\Output\Visitor\ConfigVisitor
 */
final class ConfigVisitorTest extends BaseConfigVisitorTest
{
    use TestCaseTrait;

    public function tearDown(): void
    {
        $this->closeDatabase();
    }
}
