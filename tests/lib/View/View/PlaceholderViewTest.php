<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\View;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\Placeholder;
use Netgen\BlockManager\View\View\PlaceholderView;
use PHPUnit\Framework\TestCase;

final class PlaceholderViewTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Block\Placeholder
     */
    private $placeholder;

    /**
     * @var \Netgen\BlockManager\View\View\PlaceholderViewInterface
     */
    private $view;

    /**
     * @var \Netgen\BlockManager\API\Values\Block\Block
     */
    private $block;

    public function setUp(): void
    {
        $this->block = Block::fromArray(['id' => 42]);
        $this->placeholder = Placeholder::fromArray(['identifier' => 'main']);

        $this->view = new PlaceholderView($this->placeholder, $this->block);

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('placeholder', 42);
        $this->view->addParameter('block', 42);
    }

    /**
     * @covers \Netgen\BlockManager\View\View\PlaceholderView::__construct
     * @covers \Netgen\BlockManager\View\View\PlaceholderView::getPlaceholder
     */
    public function testGetPlaceholder(): void
    {
        self::assertSame($this->placeholder, $this->view->getPlaceholder());
    }

    /**
     * @covers \Netgen\BlockManager\View\View\PlaceholderView::getBlock
     */
    public function testGetBlock(): void
    {
        self::assertSame($this->block, $this->view->getBlock());
    }

    /**
     * @covers \Netgen\BlockManager\View\View\PlaceholderView::getParameters
     */
    public function testGetParameters(): void
    {
        self::assertSame(
            [
                'placeholder' => $this->placeholder,
                'block' => $this->block,
                'param' => 'value',
            ],
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\View\PlaceholderView::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('placeholder', $this->view::getIdentifier());
    }
}
