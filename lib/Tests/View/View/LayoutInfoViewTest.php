<?php

namespace Netgen\BlockManager\Tests\View\View;

use Netgen\BlockManager\Core\Values\Page\LayoutInfo;
use Netgen\BlockManager\View\View\LayoutInfoView;
use PHPUnit\Framework\TestCase;

class LayoutInfoViewTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Page\LayoutInfo
     */
    protected $layout;

    /**
     * @var \Netgen\BlockManager\View\View\LayoutInfoViewInterface
     */
    protected $view;

    public function setUp()
    {
        $this->layout = new LayoutInfo(array('id' => 42));

        $this->view = new LayoutInfoView($this->layout);
        $this->view->addParameters(array('param' => 'value'));
        $this->view->addParameters(array('layout' => 42));
    }

    /**
     * @covers \Netgen\BlockManager\View\View\LayoutInfoView::__construct
     * @covers \Netgen\BlockManager\View\View\LayoutInfoView::getLayout
     */
    public function testGetLayout()
    {
        $this->assertEquals($this->layout, $this->view->getLayout());
        $this->assertEquals(
            array(
                'param' => 'value',
                'layout' => $this->layout,
            ),
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\View\LayoutInfoView::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('layout_info_view', $this->view->getIdentifier());
    }
}
