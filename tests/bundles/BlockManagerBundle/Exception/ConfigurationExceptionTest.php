<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Exception;

use Netgen\Bundle\BlockManagerBundle\Exception\ConfigurationException;
use PHPUnit\Framework\TestCase;

final class ConfigurationExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Exception\ConfigurationException::noParameter
     */
    public function testNoParameter(): void
    {
        $exception = ConfigurationException::noParameter('test');

        $this->assertSame(
            'Parameter "test" does not exist in configuration.',
            $exception->getMessage()
        );
    }
}
