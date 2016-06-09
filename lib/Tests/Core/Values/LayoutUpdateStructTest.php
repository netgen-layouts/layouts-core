<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\API\Values\LayoutUpdateStruct;

class LayoutUpdateStructTest extends \PHPUnit\Framework\TestCase
{
    public function testDefaultProperties()
    {
        $layoutUpdateStruct = new LayoutUpdateStruct();

        self::assertNull($layoutUpdateStruct->name);
    }

    public function testSetProperties()
    {
        $layoutUpdateStruct = new LayoutUpdateStruct(
            array(
                'name' => 'My layout',
            )
        );

        self::assertEquals('My layout', $layoutUpdateStruct->name);
    }
}
