<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Transfer;

use Netgen\Layouts\Exception\Transfer\TransferException;
use PHPUnit\Framework\TestCase;

final class TransferExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Exception\Transfer\TransferException::noEntityHandler
     */
    public function testNoEntityHandler(): void
    {
        $exception = TransferException::noEntityHandler('type');

        self::assertSame(
            'Entity handler for "type" entity type does not exist.',
            $exception->getMessage(),
        );
    }
}
