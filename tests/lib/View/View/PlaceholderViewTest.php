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

    public function setUp(): void
    {
        $this->placeholder = new Placeholder(['identifier' => 'main']);

        $this->view = new PlaceholderView(
            [
                'placeholder' => $this->placeholder,
                'block' => new Block(['id' => 42]),
            ]
        );

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
        $this->assertEquals($this->placeholder, $this->view->getPlaceholder());
    }

    /**
     * @covers \Netgen\BlockManager\View\View\PlaceholderView::getBlock
     */
    public function testGetBlock(): void
    {
        $this->assertEquals(new Block(['id' => 42]), $this->view->getBlock());
    }

    /**
     * @covers \Netgen\BlockManager\View\View\PlaceholderView::getParameters
     */
    public function testGetParameters(): void
    {
        $this->assertEquals(
            [
                'param' => 'value',
                'placeholder' => $this->placeholder,
                'block' => new Block(['id' => 42]),
            ],
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\View\PlaceholderView::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        $this->assertEquals('placeholder_view', $this->view->getIdentifier());
    }
}
