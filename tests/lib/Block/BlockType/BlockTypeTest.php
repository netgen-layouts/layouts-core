<?php

namespace Netgen\BlockManager\Tests\Block\BlockType;

use Netgen\BlockManager\Block\BlockType\BlockType;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use PHPUnit\Framework\TestCase;

class BlockTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockType\BlockType
     */
    protected $blockType;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    protected $blockDefinition;

    public function setUp()
    {
        $this->blockDefinition = new BlockDefinition('title');

        $this->blockType = new BlockType(
            array(
                'identifier' => 'title',
                'name' => 'Title',
                'icon' => '/icon.svg',
                'isEnabled' => false,
                'definition' => $this->blockDefinition,
                'defaults' => array(
                    'name' => 'Name',
                    'view_type' => 'default',
                    'item_view_type' => 'standard',
                    'parameters' => array('tag' => 'h3'),
                ),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::__construct
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('title', $this->blockType->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::isEnabled
     */
    public function testIsEnabled()
    {
        $this->assertFalse($this->blockType->isEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getName
     */
    public function testGetName()
    {
        $this->assertEquals('Title', $this->blockType->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getIcon
     */
    public function testGetIcon()
    {
        $this->assertEquals('/icon.svg', $this->blockType->getIcon());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getDefinition
     */
    public function testGetDefinition()
    {
        $this->assertEquals($this->blockDefinition, $this->blockType->getDefinition());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getDefaults
     */
    public function testGetDefaults()
    {
        $this->assertEquals(
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
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getDefaultName
     */
    public function testGetDefaultName()
    {
        $this->assertEquals('Name', $this->blockType->getDefaultName());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getDefaultViewType
     */
    public function testGetDefaultViewType()
    {
        $this->assertEquals('default', $this->blockType->getDefaultViewType());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getDefaultItemViewType
     */
    public function testGetDefaultItemViewType()
    {
        $this->assertEquals('standard', $this->blockType->getDefaultItemViewType());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getDefaultParameters
     */
    public function testGetDefaultParameters()
    {
        $this->assertEquals(array('tag' => 'h3'), $this->blockType->getDefaultParameters());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getDefaultName
     */
    public function testGetDefaultEmptyName()
    {
        $this->blockType = new BlockType();

        $this->assertEquals('', $this->blockType->getDefaultName());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getDefaultViewType
     */
    public function testGetDefaultEmptyViewType()
    {
        $this->blockType = new BlockType();

        $this->assertEquals('', $this->blockType->getDefaultViewType());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getDefaultViewType
     */
    public function testGetDefaultEmptyItemViewType()
    {
        $this->blockType = new BlockType();

        $this->assertEquals('', $this->blockType->getDefaultItemViewType());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getDefaultParameters
     */
    public function testGetDefaultEmptyParameters()
    {
        $this->blockType = new BlockType();

        $this->assertEquals(array(), $this->blockType->getDefaultParameters());
    }
}
