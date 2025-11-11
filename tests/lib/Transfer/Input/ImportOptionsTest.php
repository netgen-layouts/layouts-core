<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Input;

use Netgen\Layouts\Transfer\Input\ImportMode;
use Netgen\Layouts\Transfer\Input\ImportOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ImportOptions::class)]
final class ImportOptionsTest extends TestCase
{
    private ImportOptions $importOptions;

    protected function setUp(): void
    {
        $this->importOptions = new ImportOptions();
    }

    public function testDefaultMode(): void
    {
        self::assertTrue($this->importOptions->copyExisting());
        self::assertFalse($this->importOptions->overwriteExisting());
        self::assertFalse($this->importOptions->skipExisting());
    }

    public function testCopyMode(): void
    {
        $this->importOptions->setMode(ImportMode::Copy);

        self::assertTrue($this->importOptions->copyExisting());
        self::assertFalse($this->importOptions->overwriteExisting());
        self::assertFalse($this->importOptions->skipExisting());
    }

    public function testOverwriteMode(): void
    {
        $this->importOptions->setMode(ImportMode::Overwrite);

        self::assertFalse($this->importOptions->copyExisting());
        self::assertTrue($this->importOptions->overwriteExisting());
        self::assertFalse($this->importOptions->skipExisting());
    }

    public function testSkipMode(): void
    {
        $this->importOptions->setMode(ImportMode::Skip);

        self::assertFalse($this->importOptions->copyExisting());
        self::assertFalse($this->importOptions->overwriteExisting());
        self::assertTrue($this->importOptions->skipExisting());
    }
}
