<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\Tests\Core\Stubs\ValueWithLazyProperty;
use PHPUnit\Framework\TestCase;

final class LazyPropertyTraitTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Tests\Core\Stubs\ValueWithLazyProperty
     */
    private $value;

    public function setUp(): void
    {
        $this->value = new ValueWithLazyProperty(function (): int { return 42; });
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\LazyPropertyTrait::getLazyProperty
     */
    public function testGetLazyProperty(): void
    {
        $this->assertInternalType('callable', $this->value->value);

        $this->assertEquals(42, $this->value->getValue());

        $this->assertInternalType('int', $this->value->value);
        $this->assertEquals(42, $this->value->value);
    }
}
