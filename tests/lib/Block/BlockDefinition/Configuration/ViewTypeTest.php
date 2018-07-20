<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Configuration;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use PHPUnit\Framework\TestCase;

final class ViewTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType
     */
    private $viewType;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType
     */
    private $itemViewType1;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType
     */
    private $itemViewType2;

    public function setUp(): void
    {
        $this->itemViewType1 = ItemViewType::fromArray(['identifier' => 'standard']);
        $this->itemViewType2 = ItemViewType::fromArray(['identifier' => 'standard_with_intro']);

        $this->viewType = ViewType::fromArray(
            [
                'identifier' => 'large',
                'name' => 'Large',
                'itemViewTypes' => [
                    'standard' => $this->itemViewType1,
                    'standard_with_intro' => $this->itemViewType2,
                ],
                'validParameters' => ['param1', 'param2'],
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        $this->assertSame('large', $this->viewType->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType::getName
     */
    public function testGetName(): void
    {
        $this->assertSame('Large', $this->viewType->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType::getItemViewTypes
     */
    public function testGetItemViewTypes(): void
    {
        $this->assertSame(
            [
                'standard' => $this->itemViewType1,
                'standard_with_intro' => $this->itemViewType2,
            ],
            $this->viewType->getItemViewTypes()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType::getItemViewTypeIdentifiers
     */
    public function testGetItemViewTypeIdentifiers(): void
    {
        $this->assertSame(
            ['standard', 'standard_with_intro'],
            $this->viewType->getItemViewTypeIdentifiers()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType::hasItemViewType
     */
    public function testHasItemViewType(): void
    {
        $this->assertTrue($this->viewType->hasItemViewType('standard'));
        $this->assertFalse($this->viewType->hasItemViewType('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType::getItemViewType
     */
    public function testGetItemViewType(): void
    {
        $this->assertSame(
            $this->itemViewType1,
            $this->viewType->getItemViewType('standard')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType::getItemViewType
     * @expectedException \Netgen\BlockManager\Exception\Block\BlockDefinitionException
     * @expectedExceptionMessage Item view type "unknown" does not exist in "large" view type.
     */
    public function testGetItemViewTypeThrowsBlockDefinitionException(): void
    {
        $this->viewType->getItemViewType('unknown');
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType::getValidParameters
     */
    public function testGetValidParameters(): void
    {
        $this->assertSame(
            ['param1', 'param2'],
            $this->viewType->getValidParameters()
        );
    }
}
