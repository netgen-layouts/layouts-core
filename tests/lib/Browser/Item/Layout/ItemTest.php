<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Browser\Item\Layout;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Browser\Item\Layout\Item;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class ItemTest extends TestCase
{
    private Layout $layout;

    private UuidInterface $layoutId;

    private Item $item;

    protected function setUp(): void
    {
        $this->layoutId = Uuid::uuid4();

        $this->layout = Layout::fromArray(['id' => $this->layoutId, 'name' => 'My layout']);

        $this->item = new Item($this->layout);
    }

    /**
     * @covers \Netgen\Layouts\Browser\Item\Layout\Item::__construct
     * @covers \Netgen\Layouts\Browser\Item\Layout\Item::getValue
     */
    public function testGetValue(): void
    {
        self::assertSame($this->layoutId->toString(), $this->item->getValue());
    }

    /**
     * @covers \Netgen\Layouts\Browser\Item\Layout\Item::getName
     */
    public function testGetName(): void
    {
        self::assertSame('My layout', $this->item->getName());
    }

    /**
     * @covers \Netgen\Layouts\Browser\Item\Layout\Item::isVisible
     */
    public function testIsVisible(): void
    {
        self::assertTrue($this->item->isVisible());
    }

    /**
     * @covers \Netgen\Layouts\Browser\Item\Layout\Item::isSelectable
     */
    public function testIsSelectable(): void
    {
        self::assertTrue($this->item->isSelectable());
    }

    /**
     * @covers \Netgen\Layouts\Browser\Item\Layout\Item::getLayout
     */
    public function testGetLayout(): void
    {
        self::assertSame($this->layout, $this->item->getLayout());
    }
}
