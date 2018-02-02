<?php

namespace Netgen\BlockManager\Tests\Exception\Collection;

use Netgen\BlockManager\Exception\Collection\ItemDefinitionException;
use PHPUnit\Framework\TestCase;

final class ItemDefinitionExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Collection\ItemDefinitionException::noItemDefinition
     */
    public function testNoItemDefinition()
    {
        $exception = ItemDefinitionException::noItemDefinition('type');

        $this->assertEquals(
            'Item definition for "type" value type does not exist.',
            $exception->getMessage()
        );
    }
}