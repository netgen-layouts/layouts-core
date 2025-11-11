<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Transfer;

use Exception;
use Netgen\Layouts\Exception\Transfer\ImportException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ImportException::class)]
final class ImportExceptionTest extends TestCase
{
    public function testImportError(): void
    {
        $exception = ImportException::importError(new Exception('Test exception'));

        self::assertSame(
            'There was an error importing entities: Test exception',
            $exception->getMessage(),
        );
    }
}
