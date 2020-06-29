<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Transfer;

use Netgen\Layouts\Exception\Transfer\TransferException;
use PHPUnit\Framework\TestCase;

final class TransferExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Exception\Transfer\TransferException::noEntityLoader
     */
    public function testNoEntityLoader(): void
    {
        $exception = TransferException::noEntityLoader('type');

        self::assertSame(
            'Entity loader for "type" entity type does not exist.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\Layouts\Exception\Transfer\TransferException::noEntityImporter
     */
    public function testNoEntityImporter(): void
    {
        $exception = TransferException::noEntityImporter('type');

        self::assertSame(
            'Entity importer for "type" entity type does not exist.',
            $exception->getMessage()
        );
    }
}
