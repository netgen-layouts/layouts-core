<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\API\Values;

use Netgen\BlockManager\Tests\API\Stubs\ValueWithLazyProperty;
use PHPUnit\Framework\TestCase;

final class LazyPropertyTraitTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Tests\API\Stubs\ValueWithLazyProperty
     */
    private $value;

    public function setUp(): void
    {
        $this->value = new ValueWithLazyProperty(function (): int { return 42; });
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\LazyPropertyTrait::getLazyProperty
     */
    public function testGetLazyProperty(): void
    {
        self::assertInternalType('callable', $this->value->value);

        self::assertSame(42, $this->value->getValue());

        /** @var int $newValue */
        $newValue = $this->value->value;

        self::assertInternalType('int', $newValue);
        self::assertSame(42, $newValue);
    }
}
