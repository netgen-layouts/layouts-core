<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\CollectionVisitorTestBase;
use Netgen\Layouts\Transfer\Output\Visitor\CollectionVisitor;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CollectionVisitor::class)]
final class CollectionVisitorTest extends CollectionVisitorTestBase
{
    use TestCaseTrait;
}
