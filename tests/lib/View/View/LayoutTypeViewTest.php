<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\View;

use Netgen\Layouts\Layout\Type\LayoutType;
use Netgen\Layouts\View\View\LayoutTypeView;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LayoutTypeView::class)]
final class LayoutTypeViewTest extends TestCase
{
    private LayoutType $layoutType;

    private LayoutTypeView $view;

    protected function setUp(): void
    {
        $this->layoutType = LayoutType::fromArray(['identifier' => 'layout']);

        $this->view = new LayoutTypeView($this->layoutType);

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('layout_type', 42);
    }

    public function testGetLayoutType(): void
    {
        self::assertSame($this->layoutType, $this->view->getLayoutType());
        self::assertSame(
            [
                'param' => 'value',
                'layout_type' => $this->layoutType,
            ],
            $this->view->getParameters(),
        );
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('layout', $this->view::getIdentifier());
    }
}
