<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\API;

use Netgen\Layouts\Exception\API\LayoutException;
use PHPUnit\Framework\TestCase;

final class LayoutExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Exception\API\LayoutException::noZone
     */
    public function testNoZone(): void
    {
        $exception = LayoutException::noZone('zone');

        self::assertSame(
            'Zone with "zone" identifier does not exist in the layout.',
            $exception->getMessage(),
        );
    }
}
