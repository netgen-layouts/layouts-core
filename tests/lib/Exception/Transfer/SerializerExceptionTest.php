<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Transfer;

use Netgen\Layouts\Exception\Transfer\SerializerException;
use PHPUnit\Framework\TestCase;

final class SerializerExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Exception\Transfer\SerializerException::noEntityLoader
     */
    public function testNoEntityLoader(): void
    {
        $exception = SerializerException::noEntityLoader('type');

        self::assertSame(
            'Entity loader for "type" entity type does not exist.',
            $exception->getMessage()
        );
    }
}
