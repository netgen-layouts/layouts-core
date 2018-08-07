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
        self::assertSame('title', $this->blockType->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::isEnabled
     */
    public function testIsEnabled(): void
    {
        self::assertFalse($this->blockType->isEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getName
     */
    public function testGetName(): void
    {
        self::assertSame('Title', $this->blockType->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getIcon
     */
    public function testGetIcon(): void
    {
        self::assertSame('/icon.svg', $this->blockType->getIcon());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getDefinition
     */
    public function testGetDefinition(): void
    {
        self::assertSame($this->blockDefinition, $this->blockType->getDefinition());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getDefaults
     */
    public function testGetDefaults(): void
    {
        self::assertSame(
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
        self::assertSame('Name', $this->blockType->getDefaultName());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getDefaultViewType
     */
    public function testGetDefaultViewType(): void
    {
        self::assertSame('default', $this->blockType->getDefaultViewType());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getDefaultItemViewType
     */
    public function testGetDefaultItemViewType(): void
    {
        self::assertSame('standard', $this->blockType->getDefaultItemViewType());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getDefaultParameters
     */
    public function testGetDefaultParameters(): void
    {
        self::assertSame(['tag' => 'h3'], $this->blockType->getDefaultParameters());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getDefaultName
     */
    public function testGetDefaultEmptyName(): void
    {
        $this->blockType = new BlockType();

        self::assertSame('', $this->blockType->getDefaultName());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getDefaultViewType
     */
    public function testGetDefaultEmptyViewType(): void
    {
        $this->blockType = new BlockType();

        self::assertSame('', $this->blockType->getDefaultViewType());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getDefaultViewType
     */
    public function testGetDefaultEmptyItemViewType(): void
    {
        $this->blockType = new BlockType();

        self::assertSame('', $this->blockType->getDefaultItemViewType());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockType::getDefaultParameters
     */
    public function testGetDefaultEmptyParameters(): void
    {
        $this->blockType = new BlockType();

        self::assertSame([], $this->blockType->getDefaultParameters());
    }
}
