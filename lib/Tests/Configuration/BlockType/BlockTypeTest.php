<?php

namespace Netgen\BlockManager\Tests\Configuration\BlockType;

use Netgen\BlockManager\Configuration\BlockType\BlockType;
use PHPUnit\Framework\TestCase;

class BlockTypeTest extends TestCase
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
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockType::getDefaultName
     */
    public function testGetDefaultName()
    {
        self::assertEquals('Name', $this->blockType->getDefaultName());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockType::getDefaultViewType
     */
    public function testGetDefaultViewType()
    {
        self::assertEquals('default', $this->blockType->getDefaultViewType());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockType::getDefaultItemViewType
     */
    public function testGetDefaultItemViewType()
    {
        self::assertEquals('standard', $this->blockType->getDefaultItemViewType());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockType::getDefaultParameters
     */
    public function testGetDefaultParameters()
    {
        self::assertEquals(array('tag' => 'h3'), $this->blockType->getDefaultParameters());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockType::getDefaultName
     */
    public function testGetDefaultEmptyName()
    {
        $this->blockType = new BlockType('title', true, 'Title', 'title', array());

        self::assertEquals('', $this->blockType->getDefaultName());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockType::getDefaultViewType
     */
    public function testGetDefaultEmptyViewType()
    {
        $this->blockType = new BlockType('title', true, 'Title', 'title', array());

        self::assertEquals('', $this->blockType->getDefaultViewType());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockType::getDefaultViewType
     */
    public function testGetDefaultEmptyItemViewType()
    {
        $this->blockType = new BlockType('title', true, 'Title', 'title', array());

        self::assertEquals('', $this->blockType->getDefaultItemViewType());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockType::getDefaultParameters
     */
    public function testGetDefaultEmptyParameters()
    {
        $this->blockType = new BlockType('title', true, 'Title', 'title', array());

        self::assertEquals(array(), $this->blockType->getDefaultParameters());
    }
}
