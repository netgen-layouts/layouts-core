<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\View;

use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\View\View\LayoutView;
use PHPUnit\Framework\TestCase;

final class LayoutViewTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Layout\Layout
     */
    private $layout;

    /**
     * @var \Netgen\BlockManager\View\View\LayoutViewInterface
     */
    private $view;

    public function setUp(): void
    {
        $this->layout = new Layout(['id' => 42]);

        $this->view = new LayoutView($this->layout);

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('layout', 42);
    }

    /**
     * @covers \Netgen\BlockManager\View\View\LayoutView::__construct
     * @covers \Netgen\BlockManager\View\View\LayoutView::getLayout
     */
    public function testGetLayout(): void
    {
        $this->assertSame($this->layout, $this->view->getLayout());
        $this->assertSame(
            [
                'layout' => $this->layout,
                'param' => 'value',
            ],
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\View\LayoutView::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        $this->assertSame('layout', $this->view->getIdentifier());
    }
}
