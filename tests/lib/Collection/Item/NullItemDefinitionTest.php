<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Item;

use Netgen\BlockManager\Collection\Item\NullItemDefinition;
use PHPUnit\Framework\TestCase;

final class NullItemDefinitionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Collection\Item\NullItemDefinition
     */
    private $itemDefinition;

    public function setUp(): void
    {
        $this->itemDefinition = new NullItemDefinition('value');
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Item\NullItemDefinition::__construct
     * @covers \Netgen\BlockManager\Collection\Item\NullItemDefinition::getValueType
     */
    public function testGetValueType(): void
    {
        $this->assertSame('value', $this->itemDefinition->getValueType());
    }
}
