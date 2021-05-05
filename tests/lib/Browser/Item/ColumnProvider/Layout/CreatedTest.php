<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Browser\Item\ColumnProvider\Layout;

use DateTimeImmutable;
use Netgen\ContentBrowser\Tests\Stubs\Item as StubItem;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Browser\Item\ColumnProvider\Layout\Created;
use Netgen\Layouts\Browser\Item\Layout\Item;
use PHPUnit\Framework\TestCase;

final class CreatedTest extends TestCase
{
    private Created $provider;

    protected function setUp(): void
    {
        $this->provider = new Created('d.m.Y H:i:s');
    }

    /**
     * @covers \Netgen\Layouts\Browser\Item\ColumnProvider\Layout\Created::__construct
     * @covers \Netgen\Layouts\Browser\Item\ColumnProvider\Layout\Created::getValue
     */
    public function testGetValue(): void
    {
        $date = new DateTimeImmutable();
        $date = $date->setDate(2016, 7, 17);
        $date = $date->setTime(18, 15, 42);

        $item = new Item(
            Layout::fromArray(
                [
                    'created' => $date,
                ],
            ),
        );

        self::assertSame(
            '17.07.2016 18:15:42',
            $this->provider->getValue($item),
        );
    }

    /**
     * @covers \Netgen\Layouts\Browser\Item\ColumnProvider\Layout\Created::getValue
     */
    public function testGetValueWithInvalidItem(): void
    {
        self::assertNull($this->provider->getValue(new StubItem(42)));
    }
}
