<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Layout;

use Netgen\Layouts\API\Values\Layout\LayoutCreateStruct;
use PHPUnit\Framework\TestCase;

final class LayoutCreateStructTest extends TestCase
{
    /**
     * @coversNothing
     */
    public function testDefaultProperties(): void
    {
        $layoutCreateStruct = new LayoutCreateStruct();

        self::assertFalse($layoutCreateStruct->shared);
    }
}
