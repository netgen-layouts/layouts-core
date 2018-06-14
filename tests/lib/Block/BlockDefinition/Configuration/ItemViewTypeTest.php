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
        $this->itemViewType = new ItemViewType(['identifier' => 'standard', 'name' => 'Standard']);
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType::__construct
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        $this->assertEquals('standard', $this->itemViewType->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType::getName
     */
    public function testGetName(): void
    {
        $this->assertEquals('Standard', $this->itemViewType->getName());
    }
}
