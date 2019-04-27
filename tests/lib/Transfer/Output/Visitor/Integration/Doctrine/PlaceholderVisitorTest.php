<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\PlaceholderVisitorTest as BasePlaceholderVisitorTest;

/**
 * @covers \Netgen\Layouts\Transfer\Output\Visitor\PlaceholderVisitor
 */
final class PlaceholderVisitorTest extends BasePlaceholderVisitorTest
{
    use TestCaseTrait;

    protected function tearDown(): void
    {
        $this->closeDatabase();
    }
}
