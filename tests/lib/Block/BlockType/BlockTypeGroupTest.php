<?php

namespace Netgen\BlockManager\Tests\Block\BlockType;

use Netgen\BlockManager\Block\BlockType\BlockTypeGroup;
use Netgen\BlockManager\Tests\Block\Stubs\BlockType;
use PHPUnit\Framework\TestCase;

class BlockTypeGroupTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockType\BlockTypeGroup
     */
    private $blockTypeGroup;

    public function setUp()
    {
        $this->blockTypeGroup = new BlockTypeGroup(
            array(
                'identifier' => 'simple_blocks',
                'isEnabled' => false,
                'name' => 'Simple blocks',
                'blockTypes' => array(
                    new BlockType(array('isEnabled' => true, 'identifier' => 'type')),
                    new BlockType(array('isEnabled' => false, 'identifier' => 'type2')),
                ),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockTypeGroup::__construct
     * @covers \Netgen\BlockManager\Block\BlockType\BlockTypeGroup::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('simple_blocks', $this->blockTypeGroup->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockTypeGroup::isEnabled
     */
    public function testIsEnabled()
    {
        $this->assertFalse($this->blockTypeGroup->isEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockTypeGroup::getName
     */
    public function testGetName()
    {
        $this->assertEquals('Simple blocks', $this->blockTypeGroup->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockTypeGroup::getBlockTypes
     */
    public function testGetBlockTypes()
    {
        $this->assertEquals(
            array(
                new BlockType(array('isEnabled' => true, 'identifier' => 'type')),
                new BlockType(array('isEnabled' => false, 'identifier' => 'type2')),
            ),
            $this->blockTypeGroup->getBlockTypes()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockTypeGroup::getBlockTypes
     */
    public function testGetEnabledBlockTypes()
    {
        $this->assertEquals(
            array(
                new BlockType(array('isEnabled' => true, 'identifier' => 'type')),
            ),
            $this->blockTypeGroup->getBlockTypes(true)
        );
    }
}
