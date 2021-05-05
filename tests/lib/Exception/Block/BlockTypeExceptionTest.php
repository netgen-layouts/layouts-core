<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Block;

use Netgen\Layouts\Exception\Block\BlockTypeException;
use PHPUnit\Framework\TestCase;

final class BlockTypeExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Exception\Block\BlockTypeException::noBlockType
     */
    public function testNoBlockType(): void
    {
        $exception = BlockTypeException::noBlockType('type');

        self::assertSame(
            'Block type with "type" identifier does not exist.',
            $exception->getMessage(),
        );
    }

    /**
     * @covers \Netgen\Layouts\Exception\Block\BlockTypeException::noBlockTypeGroup
     */
    public function testNoBlockTypeGroup(): void
    {
        $exception = BlockTypeException::noBlockTypeGroup('type');

        self::assertSame(
            'Block type group with "type" identifier does not exist.',
            $exception->getMessage(),
        );
    }
}
