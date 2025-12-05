<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Browser\Item\ColumnProvider\Layout;

use Netgen\ContentBrowser\Tests\Stubs\Item as StubItem;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Browser\Item\ColumnProvider\Layout\Type;
use Netgen\Layouts\Browser\Item\Layout\Item;
use Netgen\Layouts\Layout\Type\LayoutType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Type::class)]
final class TypeTest extends TestCase
{
    private Type $provider;

    protected function setUp(): void
    {
        $this->provider = new Type();
    }

    public function testGetValue(): void
    {
        $item = new Item(
            Layout::fromArray(
                [
                    'layoutType' => LayoutType::fromArray(
                        [
                            'name' => 'Test layout 1',
                        ],
                    ),
                ],
            ),
        );

        self::assertSame(
            'Test layout 1',
            $this->provider->getValue($item),
        );
    }

    public function testGetValueWithInvalidItem(): void
    {
        self::assertNull($this->provider->getValue(new StubItem(42)));
    }
}
