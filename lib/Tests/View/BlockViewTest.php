<?php

namespace Netgen\BlockManager\Tests\View;

use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\View\BlockView;

class BlockViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\View\BlockViewInterface
     */
    protected $view;

    public function setUp()
    {
        $this->view = new BlockView();
    }

    /**
     * @covers \Netgen\BlockManager\View\BlockView::setBlock
     * @covers \Netgen\BlockManager\View\BlockView::getBlock
     */
    public function testSetBlock()
    {
        $block = new Block(array('id' => 42));

        $this->view->setParameters(array('block' => 42));
        $this->view->setBlock($block);

        self::assertEquals($block, $this->view->getBlock());
        self::assertEquals(array('block' => $block), $this->view->getParameters());
    }

    /**
     * @covers \Netgen\BlockManager\View\BlockView::getAlias
     */
    public function testGetAlias()
    {
        self::assertEquals('block_view', $this->view->getAlias());
    }
}
