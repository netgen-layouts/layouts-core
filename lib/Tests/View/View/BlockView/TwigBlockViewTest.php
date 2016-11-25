<?php

namespace Netgen\BlockManager\Tests\View\View\BlockView;

use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\View\View\BlockView\TwigBlockView;
use PHPUnit\Framework\TestCase;

class TwigBlockViewTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Page\Block
     */
    protected $block;

    /**
     * @var \Netgen\BlockManager\View\View\BlockViewInterface
     */
    protected $view;

    public function setUp()
    {
        $this->block = new Block(array('id' => 42));

        $this->view = new TwigBlockView($this->block, 'rendered');
        $this->view->addParameters(array('param' => 'value'));
        $this->view->addParameters(array('block' => 42));
        $this->view->addParameters(array('twig_block_content' => 'other'));
    }

    /**
     * @covers \Netgen\BlockManager\View\View\BlockView\TwigBlockView::__construct
     * @covers \Netgen\BlockManager\View\View\BlockView\TwigBlockView::getBlock
     */
    public function testGetBlock()
    {
        $this->assertEquals($this->block, $this->view->getBlock());
    }
    /**
     * @covers \Netgen\BlockManager\View\View\BlockView\TwigBlockView::getParameters
     */
    public function testGetParameters()
    {
        $this->assertEquals(
            array(
                'param' => 'value',
                'twig_block_content' => 'rendered',
                'block' => $this->block,
            ),
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\View\BlockView\TwigBlockView::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('block_view', $this->view->getIdentifier());
    }
}
