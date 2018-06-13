<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\Config;

use Netgen\BlockManager\Config\ConfigDefinition;
use Netgen\BlockManager\Core\Values\Config\Config;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Config\Config::getConfigKey
     * @covers \Netgen\BlockManager\Core\Values\Config\Config::getDefinition
     */
    public function testSetProperties()
    {
        $definition = new ConfigDefinition();

        $config = new Config(
            [
                'configKey' => 'config',
                'definition' => $definition,
            ]
        );

        $this->assertEquals('config', $config->getConfigKey());
        $this->assertEquals($definition, $config->getDefinition());
    }
}
