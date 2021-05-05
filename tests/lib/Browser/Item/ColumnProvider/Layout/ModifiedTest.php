<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Browser\Item\ColumnProvider\Layout;

use DateTimeImmutable;
use Netgen\ContentBrowser\Tests\Stubs\Item as StubItem;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Browser\Item\ColumnProvider\Layout\Modified;
use Netgen\Layouts\Browser\Item\Layout\Item;
use PHPUnit\Framework\TestCase;

final class ModifiedTest extends TestCase
{
    private Modified $provider;

    protected function setUp(): void
    {
        $this->provider = new Modified('d.m.Y H:i:s');
    }

    /**
     * @covers \Netgen\Layouts\Browser\Item\ColumnProvider\Layout\Modified::__construct
     * @covers \Netgen\Layouts\Browser\Item\ColumnProvider\Layout\Modified::getValue
     */
    public function testGetValue(): void
    {
        $date = new DateTimeImmutable();
        $date = $date->setDate(2016, 7, 17);
        $date = $date->setTime(18, 15, 42);

        $item = new Item(
            Layout::fromArray(
                [
                    'modified' => $date,
                ],
            ),
        );

        self::assertSame(
            '17.07.2016 18:15:42',
            $this->provider->getValue($item),
        );
    }

    /**
     * @covers \Netgen\Layouts\Browser\Item\ColumnProvider\Layout\Modified::getValue
     */
    public function testGetValueWithInvalidItem(): void
    {
        self::assertNull($this->provider->getValue(new StubItem(42)));
    }
}
