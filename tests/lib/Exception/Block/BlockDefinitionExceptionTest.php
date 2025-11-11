<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Block;

use Netgen\Layouts\Exception\Block\BlockDefinitionException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BlockDefinitionException::class)]
final class BlockDefinitionExceptionTest extends TestCase
{
    public function testNoForm(): void
    {
        $exception = BlockDefinitionException::noForm('def', 'form');

        self::assertSame(
            'Form "form" does not exist in "def" block definition.',
            $exception->getMessage(),
        );
    }

    public function testNoViewType(): void
    {
        $exception = BlockDefinitionException::noViewType('def', 'view_type');

        self::assertSame(
            'View type "view_type" does not exist in "def" block definition.',
            $exception->getMessage(),
        );
    }

    public function testNoItemViewType(): void
    {
        $exception = BlockDefinitionException::noItemViewType('view_type', 'item_view_type');

        self::assertSame(
            'Item view type "item_view_type" does not exist in "view_type" view type.',
            $exception->getMessage(),
        );
    }

    public function testNoCollection(): void
    {
        $exception = BlockDefinitionException::noCollection('def', 'coll');

        self::assertSame(
            'Collection "coll" does not exist in "def" block definition.',
            $exception->getMessage(),
        );
    }

    public function testNoBlockDefinition(): void
    {
        $exception = BlockDefinitionException::noBlockDefinition('def');

        self::assertSame(
            'Block definition with "def" identifier does not exist.',
            $exception->getMessage(),
        );
    }

    public function testNoPlugin(): void
    {
        $exception = BlockDefinitionException::noPlugin('def', 'ClassName');

        self::assertSame(
            'Block definition with "def" identifier does not have a plugin with "ClassName" class.',
            $exception->getMessage(),
        );
    }
}
