<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\API;

use Netgen\Layouts\Exception\API\BlockException;
use PHPUnit\Framework\TestCase;

final class BlockExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Exception\API\BlockException::noPlaceholder
     */
    public function testNoPlaceholder(): void
    {
        $exception = BlockException::noPlaceholder('placeholder');

        self::assertSame(
            'Placeholder with "placeholder" identifier does not exist in the block.',
            $exception->getMessage(),
        );
    }

    /**
     * @covers \Netgen\Layouts\Exception\API\BlockException::noCollection
     */
    public function testNoCollection(): void
    {
        $exception = BlockException::noCollection('collection');

        self::assertSame(
            'Collection with "collection" identifier does not exist in the block.',
            $exception->getMessage(),
        );
    }
}
