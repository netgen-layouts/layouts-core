<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\BlockDefinition\Configuration;

use Netgen\Layouts\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType;
use Netgen\Layouts\Exception\Block\BlockDefinitionException;
use PHPUnit\Framework\TestCase;

final class ViewTypeTest extends TestCase
{
    private ViewType $viewType;

    private ItemViewType $itemViewType1;

    private ItemViewType $itemViewType2;

    protected function setUp(): void
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
            ],
        );
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('large', $this->viewType->getIdentifier());
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType::getName
     */
    public function testGetName(): void
    {
        self::assertSame('Large', $this->viewType->getName());
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType::getItemViewTypes
     */
    public function testGetItemViewTypes(): void
    {
        self::assertSame(
            [
                'standard' => $this->itemViewType1,
                'standard_with_intro' => $this->itemViewType2,
            ],
            $this->viewType->getItemViewTypes(),
        );
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType::getItemViewTypeIdentifiers
     */
    public function testGetItemViewTypeIdentifiers(): void
    {
        self::assertSame(
            ['standard', 'standard_with_intro'],
            $this->viewType->getItemViewTypeIdentifiers(),
        );
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType::hasItemViewType
     */
    public function testHasItemViewType(): void
    {
        self::assertTrue($this->viewType->hasItemViewType('standard'));
        self::assertFalse($this->viewType->hasItemViewType('unknown'));
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType::getItemViewType
     */
    public function testGetItemViewType(): void
    {
        self::assertSame(
            $this->itemViewType1,
            $this->viewType->getItemViewType('standard'),
        );
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType::getItemViewType
     */
    public function testGetItemViewTypeThrowsBlockDefinitionException(): void
    {
        $this->expectException(BlockDefinitionException::class);
        $this->expectExceptionMessage('Item view type "unknown" does not exist in "large" view type.');

        $this->viewType->getItemViewType('unknown');
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType::getValidParameters
     */
    public function testGetValidParameters(): void
    {
        self::assertSame(
            ['param1', 'param2'],
            $this->viewType->getValidParameters(),
        );
    }
}
