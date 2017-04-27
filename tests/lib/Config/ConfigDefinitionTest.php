<?php

namespace Netgen\BlockManager\Tests\Config;

use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\Config\ConfigDefinition;
use Netgen\BlockManager\Tests\Config\Stubs\Block\HttpCacheConfigHandler;
use PHPUnit\Framework\TestCase;

class ConfigDefinitionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinition\ConfigDefinitionHandlerInterface
     */
    protected $handler;

    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinition
     */
    protected $configDefinition;

    public function setUp()
    {
        $this->handler = new HttpCacheConfigHandler();

        $this->configDefinition = new ConfigDefinition(
            array(
                'type' => 'type',
                'identifier' => 'config_definition',
                'handler' => $this->handler,
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Config\ConfigDefinition::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('config_definition', $this->configDefinition->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Config\ConfigDefinition::getType
     */
    public function testGetType()
    {
        $this->assertEquals('type', $this->configDefinition->getType());
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
