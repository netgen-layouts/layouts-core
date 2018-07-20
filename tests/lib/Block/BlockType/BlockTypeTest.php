<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block\BlockType;

use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockType\BlockType;
use PHPUnit\Framework\TestCase;

final class BlockTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockType\BlockType
     */
    private $blockType;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    private $blockDefinition;

    public function setUp(): void
    {
        $this->blockDefinition = BlockDefinition::fromArray(['identifier' => 'title']);

        $this->blockType = BlockType::fromArray(
            [
                'identifier' => 'title',
                'name' => 'Title',
                'icon' => '/icon.svg',
                'isEnabled' => false,
                'definition' => $this->blockDefinition,
                'defaults' => [
                    'name' => 'Name',
                    'view_type' => 'default',
                    'item_view_type' => 'standard',
                    'parameters' => ['tag' => 'h3'],
                ],
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        $this->assertSame('title', $this->blockType->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::isEnabled
     */
    public function testIsEnabled(): void
    {
        $this->assertFalse($this->blockType->isEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getName
     */
    public function testGetName(): void
    {
        $this->assertSame('Title', $this->blockType->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getIcon
     */
    public function testGetIcon(): void
    {
        $this->assertSame('/icon.svg', $this->blockType->getIcon());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getDefinition
     */
    public function testGetDefinition(): void
    {
        $this->assertSame($this->blockDefinition, $this->blockType->getDefinition());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getDefaults
     */
    public function testGetDefaults(): void
    {
        $this->assertSame(
            [
                'name' => 'Name',
                'view_type' => 'default',
                'item_view_type' => 'standard',
                'parameters' => ['tag' => 'h3'],
            ],
            $this->blockType->getDefaults()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getDefaultName
     */
    public function testGetDefaultName(): void
    {
        $this->assertSame('Name', $this->blockType->getDefaultName());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getDefaultViewType
     */
    public function testGetDefaultViewType(): void
    {
        $this->assertSame('default', $this->blockType->getDefaultViewType());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getDefaultItemViewType
     */
    public function testGetDefaultItemViewType(): void
    {
        $this->assertSame('standard', $this->blockType->getDefaultItemViewType());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getDefaultParameters
     */
    public function testGetDefaultParameters(): void
    {
        $this->assertSame(['tag' => 'h3'], $this->blockType->getDefaultParameters());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getDefaultName
     */
    public function testGetDefaultEmptyName(): void
    {
        $this->blockType = new BlockType();

        $this->assertSame('', $this->blockType->getDefaultName());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getDefaultViewType
     */
    public function testGetDefaultEmptyViewType(): void
    {
        $this->blockType = new BlockType();

        $this->assertSame('', $this->blockType->getDefaultViewType());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getDefaultViewType
     */
    public function testGetDefaultEmptyItemViewType(): void
    {
        $this->blockType = new BlockType();

        $this->assertSame('', $this->blockType->getDefaultItemViewType());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getDefaultParameters
     */
    public function testGetDefaultEmptyParameters(): void
    {
        $this->blockType = new BlockType();

        $this->assertSame([], $this->blockType->getDefaultParameters());
    }
}
