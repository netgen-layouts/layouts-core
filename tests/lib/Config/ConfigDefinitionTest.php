<?php

namespace Netgen\BlockManager\Tests\Config;

use Netgen\BlockManager\Config\ConfigDefinition;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinitionHandler;
use PHPUnit\Framework\TestCase;

final class ConfigDefinitionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface
     */
    private $handler;

    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinition
     */
    private $configDefinition;

    public function setUp()
    {
        $this->handler = new ConfigDefinitionHandler();

        $this->configDefinition = new ConfigDefinition(
            [
                'configKey' => 'config_definition',
                'handler' => $this->handler,
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Config\ConfigDefinition::getConfigKey
     */
    public function testGetConfigKey()
    {
        $this->assertEquals('config_definition', $this->configDefinition->getConfigKey());
    }
}
