<?php

namespace Netgen\BlockManager\Tests\Exception\Block;

use Netgen\BlockManager\Exception\Block\BlockDefinitionException;
use PHPUnit\Framework\TestCase;

class BlockDefinitionExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Block\BlockDefinitionException::noForm
     */
    public function testNoForm()
    {
        $exception = BlockDefinitionException::noForm('def', 'form');

        $this->assertEquals(
            'Form "form" does not exist in "def" block definition.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Block\BlockDefinitionException::noViewType
     */
    public function testNoViewType()
    {
        $exception = BlockDefinitionException::noViewType('def', 'view_type');

        $this->assertEquals(
            'View type "view_type" does not exist in "def" block definition.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Block\BlockDefinitionException::noItemViewType
     */
    public function testNoItemViewType()
    {
        $exception = BlockDefinitionException::noItemViewType('view_type', 'item_view_type');

        $this->assertEquals(
            'Item view type "item_view_type" does not exist in "view_type" view type.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Block\BlockDefinitionException::noCollection
     */
    public function testNoCollection()
    {
        $exception = BlockDefinitionException::noCollection('def', 'coll');

        $this->assertEquals(
            'Collection "coll" does not exist in "def" block definition.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Block\BlockDefinitionException::noBlockDefinition
     */
    public function testNoBlockDefinition()
    {
        $exception = BlockDefinitionException::noBlockDefinition('def');

        $this->assertEquals(
            'Block definition with "def" identifier does not exist.',
            $exception->getMessage()
        );
    }
}
