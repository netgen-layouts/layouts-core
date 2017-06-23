<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Exception;

use Netgen\Bundle\BlockManagerBundle\Exception\ConfigurationException;
use PHPUnit\Framework\TestCase;

class ConfigurationExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Exception\ConfigurationException::noParameter
     * @expectedException \Netgen\Bundle\BlockManagerBundle\Exception\ConfigurationException
     * @expectedExceptionMessage Parameter "test" does not exist in configuration.
     */
    public function testNoParameter()
    {
        throw ConfigurationException::noParameter('test');
    }
}
