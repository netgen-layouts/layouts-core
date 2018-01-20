<?php

namespace Netgen\BlockManager\Tests\Config;

use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\Config\ConfigDefinition;
use Netgen\BlockManager\Tests\Config\Stubs\Block\HttpCacheConfigHandler;
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
        $this->handler = new HttpCacheConfigHandler();

        $this->configDefinition = new ConfigDefinition(
            array(
                'configKey' => 'config_definition',
                'handler' => $this->handler,
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Config\ConfigDefinition::getConfigKey
     */
    public function testGetConfigKey()
    {
        $this->assertEquals('config_definition', $this->configDefinition->getConfigKey());
    }

    /**
     * @covers \Netgen\BlockManager\Config\ConfigDefinition::isEnabled
     */
    public function testIsEnabled()
    {
        $this->assertTrue(
            $this->configDefinition->isEnabled(
                $this->createMock(ConfigAwareValue::class)
            )
        );
    }
}
