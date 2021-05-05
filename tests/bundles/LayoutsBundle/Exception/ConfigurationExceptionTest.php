<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Exception;

use Netgen\Bundle\LayoutsBundle\Exception\ConfigurationException;
use PHPUnit\Framework\TestCase;

final class ConfigurationExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Exception\ConfigurationException::noParameter
     */
    public function testNoParameter(): void
    {
        $exception = ConfigurationException::noParameter('test');

        self::assertSame(
            'Parameter "test" does not exist in configuration.',
            $exception->getMessage(),
        );
    }
}
