<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Item;

use Netgen\Layouts\Collection\Item\NullItemDefinition;
use PHPUnit\Framework\TestCase;

final class NullItemDefinitionTest extends TestCase
{
    private NullItemDefinition $itemDefinition;

    protected function setUp(): void
    {
        $this->itemDefinition = new NullItemDefinition('value');
    }

    /**
     * @covers \Netgen\Layouts\Collection\Item\NullItemDefinition::__construct
     * @covers \Netgen\Layouts\Collection\Item\NullItemDefinition::getValueType
     */
    public function testGetValueType(): void
    {
        self::assertSame('value', $this->itemDefinition->getValueType());
    }
}
