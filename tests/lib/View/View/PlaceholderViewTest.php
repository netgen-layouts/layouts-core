<?php

namespace Netgen\BlockManager\Tests\View\View;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\Placeholder;
use Netgen\BlockManager\View\View\PlaceholderView;
use PHPUnit\Framework\TestCase;

class PlaceholderViewTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Block\Placeholder
     */
    private $placeholder;

    /**
     * @var \Netgen\BlockManager\View\View\PlaceholderViewInterface
     */
    private $view;

    public function setUp()
    {
        $this->placeholder = new Placeholder(array('identifier' => 'main'));

        $this->view = new PlaceholderView(
            array(
                'placeholder' => $this->placeholder,
                'block' => new Block(),
            )
        );

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('placeholder', 42);
        $this->view->addParameter('block', 42);
    }

    /**
     * @covers \Netgen\BlockManager\View\View\PlaceholderView::__construct
     * @covers \Netgen\BlockManager\View\View\PlaceholderView::getPlaceholder
     */
    public function testGetPlaceholder()
    {
        $this->assertEquals($this->placeholder, $this->view->getPlaceholder());
    }

    /**
     * @covers \Netgen\BlockManager\View\View\PlaceholderView::getBlock
     */
    public function testGetBlock()
    {
        $this->assertEquals(new Block(), $this->view->getBlock());
    }

    /**
     * @covers \Netgen\BlockManager\View\View\PlaceholderView::getParameters
     */
    public function testGetParameters()
    {
        $this->assertEquals(
            array(
                'param' => 'value',
                'placeholder' => $this->placeholder,
                'block' => new Block(),
            ),
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\View\PlaceholderView::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('placeholder_view', $this->view->getIdentifier());
    }
}
