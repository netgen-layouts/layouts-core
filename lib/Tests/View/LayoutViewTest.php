<?php

namespace Netgen\BlockManager\Tests\View;

use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\View\LayoutView;

class LayoutViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\View\LayoutViewInterface
     */
    protected $view;

    public function setUp()
    {
        $this->view = new LayoutView();
    }

    /**
     * @covers \Netgen\BlockManager\View\LayoutView::setLayout
     * @covers \Netgen\BlockManager\View\LayoutView::getLayout
     */
    public function testSetLayout()
    {
        $layout = new Layout(array('id' => 42));

        $this->view->setParameters(array('layout' => 42));
        $this->view->setLayout($layout);

        self::assertEquals($layout, $this->view->getLayout());
        self::assertEquals(array('layout' => $layout), $this->view->getParameters());
    }

    /**
     * @covers \Netgen\BlockManager\View\LayoutView::getAlias
     */
    public function testGetAlias()
    {
        self::assertEquals('layout_view', $this->view->getAlias());
    }
}
