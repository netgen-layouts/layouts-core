<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;

final class DebugNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\DebugNode::getConfigurationNode
     */
    public function testDebugSettings()
    {
        $config = [
            [
                'debug' => true,
            ],
        ];

        $expectedConfig = [
            'debug' => true,
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'debug'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\DebugNode::getConfigurationNode
     */
    public function testDefaultDebugSettings()
    {
        $config = [
            [],
        ];

        $expectedConfig = [
            'debug' => false,
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'debug'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\DebugNode::getConfigurationNode
     */
    public function testDebugSettingsWithInvalidDebugConfig()
    {
        $config = [
            [
                'debug' => [],
            ],
        ];

        $this->assertConfigurationIsInvalid($config, 'Invalid type for path "netgen_block_manager.debug". Expected boolean, but got array.');
    }

    /**
     * Return the instance of ConfigurationInterface that should be used by the
     * Configuration-specific assertions in this test-case.
     *
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    protected function getConfiguration()
    {
        return new Configuration(new NetgenBlockManagerExtension());
    }
}
