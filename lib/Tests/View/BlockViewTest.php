<?php

namespace Netgen\BlockManager\Tests\View;

use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\View\BlockView;

class BlockViewTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Page\Block
     */
    protected $block;

    /**
     * @var \Netgen\BlockManager\View\BlockViewInterface
     */
    protected $view;

    public function setUp()
    {
        $this->block = new Block(array('id' => 42));

        $this->view = new BlockView($this->block);
        $this->view->addParameters(array('param' => 'value'));
        $this->view->addParameters(array('block' => 42));
    }

    /**
     * @covers \Netgen\BlockManager\View\BlockView::__construct
     * @covers \Netgen\BlockManager\View\BlockView::getBlock
     */
    public function testGetBlock()
    {
        self::assertEquals($this->block, $this->view->getBlock());
        self::assertEquals(
            array(
                'param' => 'value',
                'block' => $this->block,
            ),
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\BlockView::getAlias
     */
    public function testGetAlias()
    {
        self::assertEquals('block_view', $this->view->getAlias());
    }
}
