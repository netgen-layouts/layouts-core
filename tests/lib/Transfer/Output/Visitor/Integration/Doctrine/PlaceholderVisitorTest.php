<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\PlaceholderVisitorTestBase;
use Netgen\Layouts\Transfer\Output\Visitor\PlaceholderVisitor;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PlaceholderVisitor::class)]
final class PlaceholderVisitorTest extends PlaceholderVisitorTestBase
{
    use TestCaseTrait;
}
