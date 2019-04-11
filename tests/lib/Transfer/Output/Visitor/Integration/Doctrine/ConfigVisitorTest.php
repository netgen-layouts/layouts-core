<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\ConfigVisitorTest as BaseConfigVisitorTest;

/**
 * @covers \Netgen\Layouts\Transfer\Output\Visitor\ConfigVisitor
 */
final class ConfigVisitorTest extends BaseConfigVisitorTest
{
    use TestCaseTrait;

    public function tearDown(): void
    {
        $this->closeDatabase();
    }
}
