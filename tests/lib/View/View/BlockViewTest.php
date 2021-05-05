<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\View;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\View\View\BlockView;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class BlockViewTest extends TestCase
{
    private Block $block;

    private BlockView $view;

    protected function setUp(): void
    {
        $this->block = Block::fromArray(['id' => Uuid::uuid4()]);

        $this->view = new BlockView($this->block);

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('block', 42);
    }

    /**
     * @covers \Netgen\Layouts\View\View\BlockView::__construct
     * @covers \Netgen\Layouts\View\View\BlockView::getBlock
     */
    public function testGetBlock(): void
    {
        self::assertSame($this->block, $this->view->getBlock());
    }

    /**
     * @covers \Netgen\Layouts\View\View\BlockView::getParameters
     */
    public function testGetParameters(): void
    {
        self::assertSame(
            [
                'block' => $this->block,
                'param' => 'value',
            ],
            $this->view->getParameters(),
        );
    }

    /**
     * @covers \Netgen\Layouts\View\View\BlockView::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('block', $this->view::getIdentifier());
    }
}
