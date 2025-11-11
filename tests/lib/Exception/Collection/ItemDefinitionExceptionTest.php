<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Collection;

use Netgen\Layouts\Exception\Collection\ItemDefinitionException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ItemDefinitionException::class)]
final class ItemDefinitionExceptionTest extends TestCase
{
    public function testNoItemDefinition(): void
    {
        $exception = ItemDefinitionException::noItemDefinition('type');

        self::assertSame(
            'Item definition for "type" value type does not exist.',
            $exception->getMessage(),
        );
    }
}
