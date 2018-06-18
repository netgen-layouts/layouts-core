<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Exception;

use Netgen\BlockManager\Exception\NotFoundException;
use PHPUnit\Framework\TestCase;

final class NotFoundExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\NotFoundException::__construct
     */
    public function testExceptionMessage(): void
    {
        $exception = new NotFoundException('test');

        $this->assertSame('Could not find test', $exception->getMessage());
    }

    /**
     * @covers \Netgen\BlockManager\Exception\NotFoundException::__construct
     */
    public function testExceptionMessageWithIdentifier(): void
    {
        $exception = new NotFoundException('test', 1);

        $this->assertSame(
            'Could not find test with identifier "1"',
            $exception->getMessage()
        );
    }
}
