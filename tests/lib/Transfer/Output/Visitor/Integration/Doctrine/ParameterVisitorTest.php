<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\ParameterVisitorTest as BaseParameterVisitorTest;

/**
 * @covers \Netgen\Layouts\Transfer\Output\Visitor\ParameterVisitor
 */
final class ParameterVisitorTest extends BaseParameterVisitorTest
{
    use TestCaseTrait;

    protected function tearDown(): void
    {
        $this->closeDatabase();
    }
}
