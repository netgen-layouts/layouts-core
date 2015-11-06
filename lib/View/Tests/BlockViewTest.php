<?php

namespace Netgen\BlockManager\View\Tests;

use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\View\BlockView;
use PHPUnit_Framework_TestCase;

class BlockViewTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\View\BlockView::setBlock
     * @covers \Netgen\BlockManager\View\BlockView::getBlock
     */
    public function testSetBlock()
    {
        $block = new Block(array('id' => 42));

        $view = new BlockView();
        $view->setParameters(array('block' => 42));
        $view->setBlock($block);

        self::assertEquals($block, $view->getBlock());
        self::assertEquals(array('block' => $block), $view->getParameters());
    }
}
