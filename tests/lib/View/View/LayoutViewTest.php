<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\View;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\View\View\LayoutView;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class LayoutViewTest extends TestCase
{
    private Layout $layout;

    private LayoutView $view;

    protected function setUp(): void
    {
        $this->layout = Layout::fromArray(['id' => Uuid::uuid4()]);

        $this->view = new LayoutView($this->layout);

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('layout', 42);
    }

    /**
     * @covers \Netgen\Layouts\View\View\LayoutView::__construct
     * @covers \Netgen\Layouts\View\View\LayoutView::getLayout
     */
    public function testGetLayout(): void
    {
        self::assertSame($this->layout, $this->view->getLayout());
        self::assertSame(
            [
                'layout' => $this->layout,
                'param' => 'value',
            ],
            $this->view->getParameters(),
        );
    }

    /**
     * @covers \Netgen\Layouts\View\View\LayoutView::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('layout', $this->view::getIdentifier());
    }
}
