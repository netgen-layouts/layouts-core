<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Exception;

use Netgen\Bundle\BlockManagerBundle\Exception\ConfigurationException;
use PHPUnit\Framework\TestCase;

final class ConfigurationExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Exception\ConfigurationException::noParameter
     */
    public function testNoParameter()
    {
        $exception = ConfigurationException::noParameter('test');

        $this->assertEquals(
            'Parameter "test" does not exist in configuration.',
            $exception->getMessage()
        );
    }
}
