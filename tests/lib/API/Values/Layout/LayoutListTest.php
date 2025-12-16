<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Layout;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\LayoutList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(LayoutList::class)]
final class LayoutListTest extends TestCase
{
    public function testGetLayouts(): void
    {
        $layouts = [new Layout(), new Layout()];

        self::assertSame($layouts, new LayoutList($layouts)->getLayouts());
    }

    public function testGetLayoutIds(): void
    {
        $uuid1 = Uuid::v4();
        $uuid2 = Uuid::v4();

        $layouts = [Layout::fromArray(['id' => $uuid1]), Layout::fromArray(['id' => $uuid2])];

        self::assertSame([$uuid1, $uuid2], new LayoutList($layouts)->getLayoutIds());
    }
}
