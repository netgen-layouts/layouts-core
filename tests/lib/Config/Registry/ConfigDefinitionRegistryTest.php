<?php

namespace Netgen\BlockManager\Tests\Config\Registry;

use Netgen\BlockManager\Config\Registry\ConfigDefinitionRegistry;
use Netgen\BlockManager\Tests\Config\Stubs\Block\HttpCacheConfigHandler;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinition;
use PHPUnit\Framework\TestCase;

class ConfigDefinitionRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinitionInterface
     */
    protected $configDefinition;

    /**
     * @var \Netgen\BlockManager\Config\Registry\ConfigDefinitionRegistry
     */
    protected $registry;

    public function setUp()
    {
        $this->registry = new ConfigDefinitionRegistry();

        $this->configDefinition = new ConfigDefinition(
            'block',
            'http_cache',
            new HttpCacheConfigHandler()
        );

        $this->registry->addConfigDefinition('block', $this->configDefinition);
    }

    /**
     * @covers \Netgen\BlockManager\Config\Registry\ConfigDefinitionRegistry::addConfigDefinition
     * @covers \Netgen\BlockManager\Config\Registry\ConfigDefinitionRegistry::getConfigDefinitions
     */
    public function testGetConfigDefinitions()
    {
        $this->assertEquals(array($this->configDefinition), $this->registry->getConfigDefinitions('block'));
    }

    /**
     * @covers \Netgen\BlockManager\Config\Registry\ConfigDefinitionRegistry::getConfigDefinitions
     */
    public function testGetConfigDefinitionsWithNoType()
    {
        $this->assertEquals(array(), $this->registry->getConfigDefinitions('unknown'));
    }
}
