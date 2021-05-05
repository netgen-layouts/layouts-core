<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Transfer;

use Exception;
use Netgen\Layouts\Exception\Transfer\ImportException;
use PHPUnit\Framework\TestCase;

final class ImportExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Exception\Transfer\ImportException::importError
     */
    public function testImportError(): void
    {
        $exception = ImportException::importError(new Exception('Test exception'));

        self::assertSame(
            'There was an error importing entities: Test exception',
            $exception->getMessage(),
        );
    }
}
