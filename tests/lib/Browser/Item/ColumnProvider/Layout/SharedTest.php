<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Browser\Item\ColumnProvider\Layout;

use Netgen\ContentBrowser\Tests\Stubs\Item as StubItem;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Browser\Item\ColumnProvider\Layout\Shared;
use Netgen\Layouts\Browser\Item\Layout\Item;
use PHPUnit\Framework\TestCase;

final class SharedTest extends TestCase
{
    private Shared $provider;

    protected function setUp(): void
    {
        $this->provider = new Shared();
    }

    /**
     * @covers \Netgen\Layouts\Browser\Item\ColumnProvider\Layout\Shared::getValue
     */
    public function testGetValue(): void
    {
        $item = new Item(
            Layout::fromArray(
                [
                    'shared' => true,
                ],
            ),
        );

        self::assertSame(
            'Yes',
            $this->provider->getValue($item),
        );
    }

    /**
     * @covers \Netgen\Layouts\Browser\Item\ColumnProvider\Layout\Shared::getValue
     */
    public function testGetValueWithInvalidItem(): void
    {
        self::assertNull($this->provider->getValue(new StubItem(42)));
    }
}
