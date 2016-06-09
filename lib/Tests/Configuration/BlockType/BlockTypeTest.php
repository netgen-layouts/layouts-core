<?php

namespace Netgen\BlockManager\Tests\Configuration\BlockType;

use Netgen\BlockManager\Configuration\BlockType\BlockType;

class BlockTypeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Netgen\BlockManager\Configuration\BlockType\BlockType
     */
    protected $blockType;

    public function setUp()
    {
        $this->blockType = new BlockType(
            'title',
            true,
            'Title',
            'title',
            array(
                'name' => 'Name',
                'view_type' => 'default',
                'item_view_type' => 'standard',
                'parameters' => array('tag' => 'h3'),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockType::__construct
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockType::getIdentifier
     */
    public function testGetIdentifier()
    {
        self::assertEquals('title', $this->blockType->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockType::isEnabled
     */
    public function testGetIsEnabled()
    {
        self::assertEquals(true, $this->blockType->isEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockType::getName
     */
    public function testGetName()
    {
        self::assertEquals('Title', $this->blockType->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockType::getDefinitionIdentifier
     */
    public function testGetDefinitionIdentifier()
    {
        self::assertEquals('title', $this->blockType->getDefinitionIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockType::getDefaults
     */
    public function testGetDefaults()
    {
        self::assertEquals(
            array(
                'name' => 'Name',
                'view_type' => 'default',
                'item_view_type' => 'standard',
                'parameters' => array('tag' => 'h3'),
            ),
            $this->blockType->getDefaults()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockType::getDefaultBlockName
     */
    public function testGetDefaultBlockName()
    {
        self::assertEquals('Name', $this->blockType->getDefaultBlockName());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockType::getDefaultBlockViewType
     */
    public function testGetDefaultBlockViewType()
    {
        self::assertEquals('default', $this->blockType->getDefaultBlockViewType());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockType::getDefaultBlockItemViewType
     */
    public function testGetDefaultBlockItemViewType()
    {
        self::assertEquals('standard', $this->blockType->getDefaultBlockItemViewType());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockType::getDefaultBlockParameters
     */
    public function testGetDefaultBlockParameters()
    {
        self::assertEquals(array('tag' => 'h3'), $this->blockType->getDefaultBlockParameters());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockType::getDefaultBlockName
     */
    public function testGetDefaultEmptyBlockName()
    {
        $this->blockType = new BlockType('title', true, 'Title', 'title', array());

        self::assertEquals('', $this->blockType->getDefaultBlockName());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockType::getDefaultBlockViewType
     */
    public function testGetDefaultEmptyBlockViewType()
    {
        $this->blockType = new BlockType('title', true, 'Title', 'title', array());

        self::assertEquals('', $this->blockType->getDefaultBlockViewType());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockType::getDefaultBlockParameters
     */
    public function testGetDefaultEmptyBlockParameters()
    {
        $this->blockType = new BlockType('title', true, 'Title', 'title', array());

        self::assertEquals(array(), $this->blockType->getDefaultBlockParameters());
    }
}
