<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Exception\Core;

use Netgen\BlockManager\Exception\Core\BlockException;
use PHPUnit\Framework\TestCase;

final class BlockExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Core\BlockException::noPlaceholder
     */
    public function testNoPlaceholder(): void
    {
        $exception = BlockException::noPlaceholder('placeholder');

        self::assertSame(
            'Placeholder with "placeholder" identifier does not exist in the block.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Core\BlockException::noCollection
     */
    public function testNoCollection(): void
    {
        $exception = BlockException::noCollection('collection');

        self::assertSame(
            'Collection with "collection" identifier does not exist in the block.',
            $exception->getMessage()
        );
    }
}
