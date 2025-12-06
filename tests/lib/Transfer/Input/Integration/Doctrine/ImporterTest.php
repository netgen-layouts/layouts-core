<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Input\Integration\Doctrine;

use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\Transfer\Input\Integration\ImporterTestBase;
use Netgen\Layouts\Transfer\EntityHandler\LayoutEntityHandler;
use Netgen\Layouts\Transfer\EntityHandler\RuleEntityHandler;
use Netgen\Layouts\Transfer\EntityHandler\RuleGroupEntityHandler;
use Netgen\Layouts\Transfer\Input\Importer;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RuleEntityHandler::class)]
#[CoversClass(RuleGroupEntityHandler::class)]
#[CoversClass(LayoutEntityHandler::class)]
#[CoversClass(Importer::class)]
final class ImporterTest extends ImporterTestBase
{
    use TestCaseTrait;
}
