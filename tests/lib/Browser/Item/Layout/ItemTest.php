<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Browser\Item\Layout;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Browser\Item\Layout\Item;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(Item::class)]
final class ItemTest extends TestCase
{
    private Layout $layout;

    private Uuid $layoutId;

    private Item $item;

    protected function setUp(): void
    {
        $this->layoutId = Uuid::v4();

        $this->layout = Layout::fromArray(['id' => $this->layoutId, 'name' => 'My layout']);

        $this->item = new Item($this->layout);
    }

    public function testGetValue(): void
    {
        self::assertSame($this->layoutId->toString(), $this->item->value);
    }

    public function testGetName(): void
    {
        self::assertSame('My layout', $this->item->name);
    }

    public function testGetLayout(): void
    {
        self::assertSame($this->layout, $this->item->layout);
    }
}
