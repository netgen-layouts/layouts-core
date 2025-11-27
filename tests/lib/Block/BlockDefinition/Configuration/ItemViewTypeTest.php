<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\BlockDefinition\Configuration;

use Netgen\Layouts\Block\BlockDefinition\Configuration\ItemViewType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ItemViewType::class)]
final class ItemViewTypeTest extends TestCase
{
    private ItemViewType $itemViewType;

    protected function setUp(): void
    {
        $this->itemViewType = ItemViewType::fromArray(['identifier' => 'standard', 'name' => 'Standard']);
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('standard', $this->itemViewType->identifier);
    }

    public function testGetName(): void
    {
        self::assertSame('Standard', $this->itemViewType->name);
    }
}
