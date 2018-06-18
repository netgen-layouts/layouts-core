<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Exception\Block;

use Netgen\BlockManager\Exception\Block\BlockDefinitionException;
use PHPUnit\Framework\TestCase;

final class BlockDefinitionExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Block\BlockDefinitionException::noForm
     */
    public function testNoForm(): void
    {
        $exception = BlockDefinitionException::noForm('def', 'form');

        $this->assertSame(
            'Form "form" does not exist in "def" block definition.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Block\BlockDefinitionException::noViewType
     */
    public function testNoViewType(): void
    {
        $exception = BlockDefinitionException::noViewType('def', 'view_type');

        $this->assertSame(
            'View type "view_type" does not exist in "def" block definition.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Block\BlockDefinitionException::noItemViewType
     */
    public function testNoItemViewType(): void
    {
        $exception = BlockDefinitionException::noItemViewType('view_type', 'item_view_type');

        $this->assertSame(
            'Item view type "item_view_type" does not exist in "view_type" view type.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Block\BlockDefinitionException::noCollection
     */
    public function testNoCollection(): void
    {
        $exception = BlockDefinitionException::noCollection('def', 'coll');

        $this->assertSame(
            'Collection "coll" does not exist in "def" block definition.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Block\BlockDefinitionException::noBlockDefinition
     */
    public function testNoBlockDefinition(): void
    {
        $exception = BlockDefinitionException::noBlockDefinition('def');

        $this->assertSame(
            'Block definition with "def" identifier does not exist.',
            $exception->getMessage()
        );
    }
}
