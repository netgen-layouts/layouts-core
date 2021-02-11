<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Input;

use Netgen\Layouts\Exception\InvalidArgumentException;
use Netgen\Layouts\Transfer\Input\ImportOptions;
use PHPUnit\Framework\TestCase;

final class ImportOptionsTest extends TestCase
{
    private ImportOptions $importOptions;

    protected function setUp(): void
    {
        $this->importOptions = new ImportOptions();
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Input\ImportOptions::copyExisting
     * @covers \Netgen\Layouts\Transfer\Input\ImportOptions::overwriteExisting
     * @covers \Netgen\Layouts\Transfer\Input\ImportOptions::skipExisting
     */
    public function testDefaultMode(): void
    {
        self::assertTrue($this->importOptions->copyExisting());
        self::assertFalse($this->importOptions->overwriteExisting());
        self::assertFalse($this->importOptions->skipExisting());
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Input\ImportOptions::copyExisting
     * @covers \Netgen\Layouts\Transfer\Input\ImportOptions::overwriteExisting
     * @covers \Netgen\Layouts\Transfer\Input\ImportOptions::setMode
     * @covers \Netgen\Layouts\Transfer\Input\ImportOptions::skipExisting
     */
    public function testCopyMode(): void
    {
        $this->importOptions->setMode(ImportOptions::MODE_COPY);

        self::assertTrue($this->importOptions->copyExisting());
        self::assertFalse($this->importOptions->overwriteExisting());
        self::assertFalse($this->importOptions->skipExisting());
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Input\ImportOptions::copyExisting
     * @covers \Netgen\Layouts\Transfer\Input\ImportOptions::overwriteExisting
     * @covers \Netgen\Layouts\Transfer\Input\ImportOptions::setMode
     * @covers \Netgen\Layouts\Transfer\Input\ImportOptions::skipExisting
     */
    public function testOverwriteMode(): void
    {
        $this->importOptions->setMode(ImportOptions::MODE_OVERWRITE);

        self::assertFalse($this->importOptions->copyExisting());
        self::assertTrue($this->importOptions->overwriteExisting());
        self::assertFalse($this->importOptions->skipExisting());
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Input\ImportOptions::copyExisting
     * @covers \Netgen\Layouts\Transfer\Input\ImportOptions::overwriteExisting
     * @covers \Netgen\Layouts\Transfer\Input\ImportOptions::setMode
     * @covers \Netgen\Layouts\Transfer\Input\ImportOptions::skipExisting
     */
    public function testSkipMode(): void
    {
        $this->importOptions->setMode(ImportOptions::MODE_SKIP);

        self::assertFalse($this->importOptions->copyExisting());
        self::assertFalse($this->importOptions->overwriteExisting());
        self::assertTrue($this->importOptions->skipExisting());
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Input\ImportOptions::setMode
     */
    public function testSetInvalidModeThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Import mode "invalid" is not supported');

        $this->importOptions->setMode('invalid');
    }
}
