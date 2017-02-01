<?php

namespace Netgen\BlockManager\Tests\Core\Values\Page;

use Netgen\BlockManager\API\Values\Page\BlockCreateStruct;
use Netgen\BlockManager\API\Values\Page\PlaceholderCreateStruct;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class BlockCreateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $blockCreateStruct = new BlockCreateStruct();

        $this->assertNull($blockCreateStruct->definition);
        $this->assertNull($blockCreateStruct->viewType);
        $this->assertNull($blockCreateStruct->itemViewType);
        $this->assertNull($blockCreateStruct->name);
    }

    public function testSetProperties()
    {
        $blockCreateStruct = new BlockCreateStruct(
            array(
                'definition' => new BlockDefinition(),
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
            )
        );

        $this->assertEquals(new BlockDefinition(), $blockCreateStruct->definition);
        $this->assertEquals('default', $blockCreateStruct->viewType);
        $this->assertEquals('standard', $blockCreateStruct->itemViewType);
        $this->assertEquals('My block', $blockCreateStruct->name);
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Page\BlockCreateStruct::setPlaceholderStruct
     * @covers \Netgen\BlockManager\API\Values\Page\BlockCreateStruct::getPlaceholderStruct
     * @covers \Netgen\BlockManager\API\Values\Page\BlockCreateStruct::hasPlaceholderStruct
     * @covers \Netgen\BlockManager\API\Values\Page\BlockCreateStruct::getPlaceholderStructs
     */
    public function testGetSetPlaceholderStruct()
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->setPlaceholderStruct('main', new PlaceholderCreateStruct());
        $blockCreateStruct->setPlaceholderStruct('second', new PlaceholderCreateStruct());

        $this->assertTrue($blockCreateStruct->hasPlaceholderStruct('main'));
        $this->assertFalse($blockCreateStruct->hasPlaceholderStruct('test'));

        $this->assertEquals(
            new PlaceholderCreateStruct(),
            $blockCreateStruct->getPlaceholderStruct('main')
        );

        $this->assertEquals(
            array(
                'main' => new PlaceholderCreateStruct(),
                'second' => new PlaceholderCreateStruct(),
            ),
            $blockCreateStruct->getPlaceholderStructs()
        );

        try {
            $blockCreateStruct->getPlaceholderStruct('non_existing');
        } catch (InvalidArgumentException $e) {
            // Do nothing
        }
    }
}
