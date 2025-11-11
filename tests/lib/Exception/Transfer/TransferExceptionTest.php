<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Transfer;

use Netgen\Layouts\Exception\Transfer\TransferException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TransferException::class)]
final class TransferExceptionTest extends TestCase
{
    public function testNoEntityHandler(): void
    {
        $exception = TransferException::noEntityHandler('type');

        self::assertSame(
            'Entity handler for "type" entity type does not exist.',
            $exception->getMessage(),
        );
    }
}
