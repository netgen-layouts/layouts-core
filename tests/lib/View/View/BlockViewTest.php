<?php

namespace Netgen\BlockManager\Tests\View\View;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\View\View\BlockView;
use PHPUnit\Framework\TestCase;

final class BlockViewTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Block\Block
     */
    private $block;

    /**
     * @var \Netgen\BlockManager\View\View\BlockViewInterface
     */
    private $view;

    public function setUp()
    {
        $this->block = new Block(['id' => 42]);

        $this->view = new BlockView(
            [
                'block' => $this->block,
            ]
        );

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('block', 42);
    }

    /**
     * @covers \Netgen\BlockManager\View\View\BlockView::__construct
     * @covers \Netgen\BlockManager\View\View\BlockView::getBlock
     */
    public function testGetBlock()
    {
        $this->assertEquals($this->block, $this->view->getBlock());
    }

    /**
     * @covers \Netgen\BlockManager\View\View\BlockView::getParameters
     */
    public function testGetParameters()
    {
        $this->assertEquals(
            [
                'param' => 'value',
                'block' => $this->block,
            ],
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\View\BlockView::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('block_view', $this->view->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\View\View\BlockView::jsonSerialize
     */
    public function testJsonSerialize()
    {
        $this->assertEquals(
            [
                'blockId' => 42,
            ],
            $this->view->jsonSerialize()
        );
    }
}
