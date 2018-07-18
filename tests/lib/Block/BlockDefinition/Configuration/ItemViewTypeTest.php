<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Configuration;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use PHPUnit\Framework\TestCase;

final class ItemViewTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType
     */
    private $itemViewType;

    public function setUp(): void
    {
        $this->itemViewType = ItemViewType::fromArray(['identifier' => 'standard', 'name' => 'Standard']);
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType::__construct
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        $this->assertSame('standard', $this->itemViewType->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType::getName
     */
    public function testGetName(): void
    {
        $this->assertSame('Standard', $this->itemViewType->getName());
    }
}
