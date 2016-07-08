<?php

namespace Netgen\BlockManager\Tests\View\View;

use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\View\View\LayoutView;
use PHPUnit\Framework\TestCase;

class LayoutViewTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Page\Layout
     */
    protected $layout;

    /**
     * @var \Netgen\BlockManager\View\View\LayoutViewInterface
     */
    protected $view;

    public function setUp()
    {
        $this->layout = new Layout(array('id' => 42));

        $this->view = new LayoutView($this->layout);
        $this->view->addParameters(array('param' => 'value'));
        $this->view->addParameters(array('layout' => 42));
    }

    /**
     * @covers \Netgen\BlockManager\View\View\LayoutView::__construct
     * @covers \Netgen\BlockManager\View\View\LayoutView::getLayout
     */
    public function testGetLayout()
    {
        self::assertEquals($this->layout, $this->view->getLayout());
        self::assertEquals(
            array(
                'param' => 'value',
                'layout' => $this->layout,
            ),
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\View\LayoutView::getIdentifier
     */
    public function testGetIdentifier()
    {
        self::assertEquals('layout_view', $this->view->getIdentifier());
    }
}
