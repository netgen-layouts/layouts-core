<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Exception\Config;

use Netgen\BlockManager\Exception\Config\ConfigDefinitionException;
use PHPUnit\Framework\TestCase;

final class ConfigDefinitionExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Config\ConfigDefinitionException::noConfigDefinition
     */
    public function testNoConfigDefinition(): void
    {
        $exception = ConfigDefinitionException::noConfigDefinition('type', 'def');

        $this->assertSame(
            'Config definition for "type" type and "def" identifier does not exist.',
            $exception->getMessage()
        );
    }
}
