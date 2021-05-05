<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Browser\Item\ColumnProvider\Layout;

use Netgen\ContentBrowser\Tests\Stubs\Item as StubItem;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Browser\Item\ColumnProvider\Layout\LayoutId;
use Netgen\Layouts\Browser\Item\Layout\Item;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class LayoutIdTest extends TestCase
{
    private LayoutId $provider;

    protected function setUp(): void
    {
        $this->provider = new LayoutId();
    }

    /**
     * @covers \Netgen\Layouts\Browser\Item\ColumnProvider\Layout\LayoutId::getValue
     */
    public function testGetValue(): void
    {
        $uuid = Uuid::uuid4();

        $item = new Item(
            Layout::fromArray(
                [
                    'id' => $uuid,
                ],
            ),
        );

        self::assertSame($uuid->toString(), $this->provider->getValue($item));
    }

    /**
     * @covers \Netgen\Layouts\Browser\Item\ColumnProvider\Layout\LayoutId::getValue
     */
    public function testGetValueWithInvalidItem(): void
    {
        self::assertNull($this->provider->getValue(new StubItem(42)));
    }
}
