<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\API\Values\BlockCreateStruct;
use PHPUnit\Framework\TestCase;

class BlockCreateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $blockCreateStruct = new BlockCreateStruct();

        self::assertNull($blockCreateStruct->definitionIdentifier);
        self::assertNull($blockCreateStruct->viewType);
        self::assertNull($blockCreateStruct->itemViewType);
        self::assertNull($blockCreateStruct->name);
    }

    public function testSetProperties()
    {
        $blockCreateStruct = new BlockCreateStruct(
            array(
                'definitionIdentifier' => 'text',
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
            )
        );

        self::assertEquals('text', $blockCreateStruct->definitionIdentifier);
        self::assertEquals('default', $blockCreateStruct->viewType);
        self::assertEquals('standard', $blockCreateStruct->itemViewType);
        self::assertEquals('My block', $blockCreateStruct->name);
    }
}
