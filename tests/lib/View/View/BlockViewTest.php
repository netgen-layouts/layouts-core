<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\View;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\View\View\BlockView;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(BlockView::class)]
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

    public function testGetBlock(): void
    {
        self::assertSame($this->block, $this->view->block);
    }

    public function testGetParameters(): void
    {
        self::assertSame(
            [
                'param' => 'value',
                'block' => $this->block,
            ],
            $this->view->parameters,
        );
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('block', $this->view->identifier);
    }
}
