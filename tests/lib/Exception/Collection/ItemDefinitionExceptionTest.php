<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Collection;

use Netgen\Layouts\Exception\Collection\ItemDefinitionException;
use PHPUnit\Framework\TestCase;

final class ItemDefinitionExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Exception\Collection\ItemDefinitionException::noItemDefinition
     */
    public function testNoItemDefinition(): void
    {
        $exception = ItemDefinitionException::noItemDefinition('type');

        self::assertSame(
            'Item definition for "type" value type does not exist.',
            $exception->getMessage(),
        );
    }
}
