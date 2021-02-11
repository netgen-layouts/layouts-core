<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\BlockDefinition\Configuration;

use Netgen\Layouts\Block\BlockDefinition\Configuration\ItemViewType;
use PHPUnit\Framework\TestCase;

final class ItemViewTypeTest extends TestCase
{
    private ItemViewType $itemViewType;

    protected function setUp(): void
    {
        $this->itemViewType = ItemViewType::fromArray(['identifier' => 'standard', 'name' => 'Standard']);
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition\Configuration\ItemViewType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('standard', $this->itemViewType->getIdentifier());
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition\Configuration\ItemViewType::getName
     */
    public function testGetName(): void
    {
        self::assertSame('Standard', $this->itemViewType->getName());
    }
}
