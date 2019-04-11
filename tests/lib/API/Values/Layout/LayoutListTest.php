<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Layout;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\LayoutList;
use PHPUnit\Framework\TestCase;
use stdClass;
use TypeError;

final class LayoutListTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\API\Values\Layout\LayoutList::__construct
     */
    public function testConstructorWithInvalidType(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            sprintf(
                'Argument 1 passed to %s::%s\{closure}() must be an instance of %s, instance of %s given',
                LayoutList::class,
                str_replace('\LayoutList', '', LayoutList::class),
                Layout::class,
                stdClass::class
            )
        );

        new LayoutList([new Layout(), new stdClass(), new Layout()]);
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Layout\LayoutList::__construct
     * @covers \Netgen\Layouts\API\Values\Layout\LayoutList::getLayouts
     */
    public function testGetLayouts(): void
    {
        $layouts = [new Layout(), new Layout()];

        self::assertSame($layouts, (new LayoutList($layouts))->getLayouts());
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Layout\LayoutList::getLayoutIds
     */
    public function testGetLayoutIds(): void
    {
        $layouts = [Layout::fromArray(['id' => 42]), Layout::fromArray(['id' => 24])];

        self::assertSame([42, 24], (new LayoutList($layouts))->getLayoutIds());
    }
}
