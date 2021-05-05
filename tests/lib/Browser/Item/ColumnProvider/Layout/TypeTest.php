<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Browser\Item\ColumnProvider\Layout;

use Netgen\ContentBrowser\Tests\Stubs\Item as StubItem;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Browser\Item\ColumnProvider\Layout\Type;
use Netgen\Layouts\Browser\Item\Layout\Item;
use Netgen\Layouts\Layout\Type\LayoutType;
use PHPUnit\Framework\TestCase;

final class TypeTest extends TestCase
{
    private Type $provider;

    protected function setUp(): void
    {
        $this->provider = new Type();
    }

    /**
     * @covers \Netgen\Layouts\Browser\Item\ColumnProvider\Layout\Type::getValue
     */
    public function testGetValue(): void
    {
        $item = new Item(
            Layout::fromArray(
                [
                    'layoutType' => LayoutType::fromArray(
                        [
                            'name' => '4 zones A',
                        ],
                    ),
                ],
            ),
        );

        self::assertSame(
            '4 zones A',
            $this->provider->getValue($item),
        );
    }

    /**
     * @covers \Netgen\Layouts\Browser\Item\ColumnProvider\Layout\Type::getValue
     */
    public function testGetValueWithInvalidItem(): void
    {
        self::assertNull($this->provider->getValue(new StubItem(42)));
    }
}
