<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\Layout;

use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\Layout\Type\LayoutType;
use PHPUnit\Framework\TestCase;

final class LayoutCreateStructTest extends TestCase
{
    public function testDefaultProperties(): void
    {
        $layoutCreateStruct = new LayoutCreateStruct();

        $this->assertFalse($layoutCreateStruct->shared);
    }

    public function testSetProperties(): void
    {
        $layoutType = new LayoutType();

        $layoutCreateStruct = new LayoutCreateStruct(
            [
                'layoutType' => $layoutType,
                'name' => 'My layout',
                'description' => 'My description',
                'shared' => true,
                'mainLocale' => 'en',
            ]
        );

        $this->assertSame($layoutType, $layoutCreateStruct->layoutType);
        $this->assertSame('My layout', $layoutCreateStruct->name);
        $this->assertSame('My description', $layoutCreateStruct->description);
        $this->assertTrue($layoutCreateStruct->shared);
        $this->assertSame('en', $layoutCreateStruct->mainLocale);
    }
}
