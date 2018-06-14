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

    public function setUp(): void
    {
        $this->viewType = new ViewType(
            [
                'identifier' => 'large',
                'name' => 'Large',
                'itemViewTypes' => [
                    'standard' => new ItemViewType(['identifier' => 'standard']),
                    'standard_with_intro' => new ItemViewType(['identifier' => 'standard_with_intro']),
                ],
                'validParameters' => ['param1', 'param2'],
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType::__construct
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        $this->assertEquals('large', $this->viewType->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType::getName
     */
    public function testGetName(): void
    {
        $this->assertEquals('Large', $this->viewType->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType::getItemViewTypes
     */
    public function testGetItemViewTypes(): void
    {
        $this->assertEquals(
            [
                'standard' => new ItemViewType(['identifier' => 'standard']),
                'standard_with_intro' => new ItemViewType(['identifier' => 'standard_with_intro']),
            ],
            $this->viewType->getItemViewTypes()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType::getItemViewTypeIdentifiers
     */
    public function testGetItemViewTypeIdentifiers(): void
    {
        $this->assertEquals(
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
        $this->assertEquals(
            new ItemViewType(['identifier' => 'standard']),
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
        $this->assertEquals(
            ['param1', 'param2'],
            $this->viewType->getValidParameters()
        );
    }
}
