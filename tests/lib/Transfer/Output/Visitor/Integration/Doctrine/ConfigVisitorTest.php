<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\ConfigVisitorTestBase;
use Netgen\Layouts\Transfer\Output\Visitor\ConfigVisitor;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ConfigVisitor::class)]
final class ConfigVisitorTest extends ConfigVisitorTestBase
{
    use TestCaseTrait;
}
