<?php

namespace Netgen\BlockManager\Tests\View;

use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\View\LayoutView;

class LayoutViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\View\LayoutView::setLayout
     * @covers \Netgen\BlockManager\View\LayoutView::getLayout
     */
    public function testSetLayout()
    {
        $layout = new Layout(array('id' => 42));

        $view = new LayoutView();
        $view->setParameters(array('layout' => 42));
        $view->setLayout($layout);

        self::assertEquals($layout, $view->getLayout());
        self::assertEquals(array('layout' => $layout), $view->getParameters());
    }
}
