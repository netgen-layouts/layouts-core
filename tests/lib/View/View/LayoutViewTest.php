<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\View;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\View\View\LayoutView;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(LayoutView::class)]
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

    public function testGetLayout(): void
    {
        self::assertSame($this->layout, $this->view->getLayout());
        self::assertSame(
            [
                'param' => 'value',
                'layout' => $this->layout,
            ],
            $this->view->getParameters(),
        );
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('layout', $this->view::getIdentifier());
    }
}
