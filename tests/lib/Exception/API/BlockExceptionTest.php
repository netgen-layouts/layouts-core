<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\API;

use Netgen\Layouts\Exception\API\BlockException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BlockException::class)]
final class BlockExceptionTest extends TestCase
{
    public function testNoPlaceholder(): void
    {
        $exception = BlockException::noPlaceholder('placeholder');

        self::assertSame(
            'Placeholder with "placeholder" identifier does not exist in the block.',
            $exception->getMessage(),
        );
    }

    public function testNoCollection(): void
    {
        $exception = BlockException::noCollection('collection');

        self::assertSame(
            'Collection with "collection" identifier does not exist in the block.',
            $exception->getMessage(),
        );
    }
}
