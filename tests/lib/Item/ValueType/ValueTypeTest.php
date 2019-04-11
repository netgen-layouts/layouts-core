<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Item\ValueType;

use Netgen\Layouts\Item\ValueType\ValueType;
use PHPUnit\Framework\TestCase;

final class ValueTypeTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\Item\ValueType\ValueType
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
     * @covers \Netgen\Layouts\Item\ValueType\ValueType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('value', $this->valueType->getIdentifier());
    }

    /**
     * @covers \Netgen\Layouts\Item\ValueType\ValueType::isEnabled
     */
    public function testIsEnabled(): void
    {
        self::assertFalse($this->valueType->isEnabled());
    }

    /**
     * @covers \Netgen\Layouts\Item\ValueType\ValueType::getName
     */
    public function testGetName(): void
    {
        self::assertSame('Value type', $this->valueType->getName());
    }
}
