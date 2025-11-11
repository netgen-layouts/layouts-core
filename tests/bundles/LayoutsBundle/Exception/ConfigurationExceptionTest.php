<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Exception;

use Netgen\Bundle\LayoutsBundle\Exception\ConfigurationException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ConfigurationException::class)]
final class ConfigurationExceptionTest extends TestCase
{
    public function testNoParameter(): void
    {
        $exception = ConfigurationException::noParameter('test');

        self::assertSame(
            'Parameter "test" does not exist in configuration.',
            $exception->getMessage(),
        );
    }
}
