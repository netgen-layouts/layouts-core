<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\View;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\View\View\LayoutView;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(LayoutView::class)]
final class LayoutViewTest extends TestCase
{
    private Layout $layout;

    private LayoutView $view;

    protected function setUp(): void
    {
        $this->layout = Layout::fromArray(['id' => Uuid::v7()]);

        $this->view = new LayoutView($this->layout);

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('layout', 42);
    }

    public function testGetLayout(): void
    {
        self::assertSame($this->layout, $this->view->layout);
        self::assertSame(
            [
                'param' => 'value',
                'layout' => $this->layout,
            ],
            $this->view->parameters,
        );
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('layout', $this->view->identifier);
    }
}
