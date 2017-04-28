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

        $this->registry->addConfigDefinition('block', 'http_cache', $this->configDefinition);
    }

    /**
     * @covers \Netgen\BlockManager\Config\Registry\ConfigDefinitionRegistry::addConfigDefinition
     * @covers \Netgen\BlockManager\Config\Registry\ConfigDefinitionRegistry::getConfigDefinitions
     */
    public function testGetConfigDefinitions()
    {
        $this->assertEquals(array('http_cache' => $this->configDefinition), $this->registry->getConfigDefinitions('block'));
    }

    /**
     * @covers \Netgen\BlockManager\Config\Registry\ConfigDefinitionRegistry::getConfigDefinitions
     */
    public function testGetConfigDefinitionsWithNoType()
    {
        $this->assertEquals(array(), $this->registry->getConfigDefinitions('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Config\Registry\ConfigDefinitionRegistry::getConfigDefinition
     */
    public function testGetConfigDefinition()
    {
        $this->assertEquals($this->configDefinition, $this->registry->getConfigDefinition('block', 'http_cache'));
    }

    /**
     * @covers \Netgen\BlockManager\Config\Registry\ConfigDefinitionRegistry::getConfigDefinition
     * @expectedException \Netgen\BlockManager\Exception\Config\ConfigException
     * @expectedExceptionMessage Config definition for "block" type and "unknown" identifier does not exist.
     */
    public function testGetConfigDefinitionThrowsInvalidArgumentException()
    {
        $this->registry->getConfigDefinition('block', 'unknown');
    }

    /**
     * @covers \Netgen\BlockManager\Config\Registry\ConfigDefinitionRegistry::hasConfigDefinition
     */
    public function testHasConfigDefinition()
    {
        $this->assertTrue($this->registry->hasConfigDefinition('block', 'http_cache'));
    }

    /**
     * @covers \Netgen\BlockManager\Config\Registry\ConfigDefinitionRegistry::hasConfigDefinition
     */
    public function testHasConfigDefinitionWithNoConfigDefinition()
    {
        $this->assertFalse($this->registry->hasConfigDefinition('block', 'unknown'));
    }
}
