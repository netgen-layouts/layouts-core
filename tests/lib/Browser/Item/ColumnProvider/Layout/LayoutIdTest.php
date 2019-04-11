<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Browser\Item\ColumnProvider\Layout;

use Netgen\ContentBrowser\Tests\Stubs\Item as StubItem;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Browser\Item\ColumnProvider\Layout\LayoutId;
use Netgen\Layouts\Browser\Item\Layout\Item;
use PHPUnit\Framework\TestCase;

final class LayoutIdTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\Browser\Item\ColumnProvider\Layout\LayoutId
     */
    private $provider;

    public function setUp(): void
    {
        $this->provider = new LayoutId();
    }

    /**
     * @covers \Netgen\Layouts\Browser\Item\ColumnProvider\Layout\LayoutId::getValue
     */
    public function testGetValue(): void
    {
        $item = new Item(
            Layout::fromArray(
                [
                    'id' => 42,
                ]
            )
        );

        self::assertSame('42', $this->provider->getValue($item));
    }

    /**
     * @covers \Netgen\Layouts\Browser\Item\ColumnProvider\Layout\LayoutId::getValue
     */
    public function testGetValueWithInvalidItem(): void
    {
        self::assertNull($this->provider->getValue(new StubItem()));
    }
}
