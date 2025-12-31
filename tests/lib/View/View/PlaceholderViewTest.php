<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\View;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\Placeholder;
use Netgen\Layouts\View\View\PlaceholderView;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(PlaceholderView::class)]
final class PlaceholderViewTest extends TestCase
{
    private Placeholder $placeholder;

    private PlaceholderView $view;

    private Block $block;

    protected function setUp(): void
    {
        $this->block = Block::fromArray(['id' => Uuid::v7()]);
        $this->placeholder = Placeholder::fromArray(['identifier' => 'main']);

        $this->view = new PlaceholderView($this->placeholder, $this->block);

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('placeholder', 42);
        $this->view->addParameter('block', 42);
    }

    public function testGetPlaceholder(): void
    {
        self::assertSame($this->placeholder, $this->view->placeholder);
    }

    public function testGetBlock(): void
    {
        self::assertSame($this->block, $this->view->block);
    }

    public function testGetParameters(): void
    {
        self::assertSame(
            [
                'param' => 'value',
                'placeholder' => $this->placeholder,
                'block' => $this->block,
            ],
            $this->view->parameters,
        );
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('placeholder', $this->view->identifier);
    }
}
