<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Block;

use Netgen\Layouts\Exception\Block\BlockTypeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BlockTypeException::class)]
final class BlockTypeExceptionTest extends TestCase
{
    public function testNoBlockType(): void
    {
        $exception = BlockTypeException::noBlockType('type');

        self::assertSame(
            'Block type with "type" identifier does not exist.',
            $exception->getMessage(),
        );
    }

    public function testNoBlockTypeGroup(): void
    {
        $exception = BlockTypeException::noBlockTypeGroup('type');

        self::assertSame(
            'Block type group with "type" identifier does not exist.',
            $exception->getMessage(),
        );
    }
}
