<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Layout;

use Netgen\Layouts\API\Values\Layout\LayoutCreateStruct;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
final class LayoutCreateStructTest extends TestCase
{
    public function testDefaultProperties(): void
    {
        $layoutCreateStruct = new LayoutCreateStruct();

        self::assertFalse($layoutCreateStruct->isShared);
    }
}
