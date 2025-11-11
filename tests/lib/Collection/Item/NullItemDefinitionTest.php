<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Item;

use Netgen\Layouts\Collection\Item\NullItemDefinition;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NullItemDefinition::class)]
final class NullItemDefinitionTest extends TestCase
{
    private NullItemDefinition $itemDefinition;

    protected function setUp(): void
    {
        $this->itemDefinition = new NullItemDefinition('value');
    }

    public function testGetValueType(): void
    {
        self::assertSame('value', $this->itemDefinition->getValueType());
    }
}
