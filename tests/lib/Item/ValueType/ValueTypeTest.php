<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Item\ValueType;

use Netgen\BlockManager\Item\ValueType\ValueType;
use PHPUnit\Framework\TestCase;

final class ValueTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Item\ValueType\ValueType
     */
    private $valueType;

    public function setUp(): void
    {
        $this->valueType = ValueType::fromArray(
            [
                'identifier' => 'value',
                'isEnabled' => false,
                'name' => 'Value type',
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Item\ValueType\ValueType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        $this->assertSame('value', $this->valueType->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Item\ValueType\ValueType::isEnabled
     */
    public function testIsEnabled(): void
    {
        $this->assertFalse($this->valueType->isEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Item\ValueType\ValueType::getName
     */
    public function testGetName(): void
    {
        $this->assertSame('Value type', $this->valueType->getName());
    }
}
