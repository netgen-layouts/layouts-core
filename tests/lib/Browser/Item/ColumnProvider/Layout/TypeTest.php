<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Browser\Item\ColumnProvider\Layout;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Type;
use Netgen\BlockManager\Browser\Item\Layout\Item;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\ContentBrowser\Tests\Stubs\Item as StubItem;
use PHPUnit\Framework\TestCase;

final class TypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Type
     */
    private $provider;

    public function setUp(): void
    {
        $this->provider = new Type();
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Type::getValue
     */
    public function testGetValue(): void
    {
        $item = new Item(
            Layout::fromArray(
                [
                    'layoutType' => LayoutType::fromArray(
                        [
                            'name' => '4 zones A',
                        ]
                    ),
                ]
            )
        );

        self::assertSame(
            '4 zones A',
            $this->provider->getValue($item)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Type::getValue
     */
    public function testGetValueWithInvalidItem(): void
    {
        self::assertNull($this->provider->getValue(new StubItem()));
    }
}
