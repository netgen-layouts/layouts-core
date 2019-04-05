<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\API\Values;

use Netgen\BlockManager\Tests\API\Stubs\ValueWithLazyProperty;
use Netgen\BlockManager\Tests\TestCase\LegacyTestCaseTrait;
use PHPUnit\Framework\TestCase;

final class LazyPropertyTraitTest extends TestCase
{
    use LegacyTestCaseTrait;

    /**
     * @var \Netgen\BlockManager\Tests\API\Stubs\ValueWithLazyProperty
     */
    private $value;

    public function setUp(): void
    {
        $this->value = new ValueWithLazyProperty(
            static function (): int {
                return 42;
            }
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\LazyPropertyTrait::getLazyProperty
     */
    public function testGetLazyProperty(): void
    {
        self::assertIsCallable($this->value->value);

        self::assertSame(42, $this->value->getValue());
        self::assertSame(42, $this->value->value);
    }
}
