<?php

namespace Netgen\BlockManager\Tests\Exception\Config;

use Netgen\BlockManager\Exception\Config\ConfigDefinitionException;
use PHPUnit\Framework\TestCase;

class ConfigDefinitionExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Config\ConfigDefinitionException::noConfigDefinition
     */
    public function testNoConfigDefinition()
    {
        $exception = ConfigDefinitionException::noConfigDefinition('type', 'def');

        $this->assertEquals(
            'Config definition for "type" type and "def" identifier does not exist.',
            $exception->getMessage()
        );
    }
}
