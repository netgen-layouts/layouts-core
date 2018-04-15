<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;

final class DesignNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\DesignNode::getConfigurationNode
     */
    public function testDesignSettings()
    {
        $config = array(
            array(),
        );

        $expectedConfig = array(
            'design' => 'standard',
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'design'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\DesignNode::getConfigurationNode
     */
    public function testDesignSettingsWithEmptyDesignName()
    {
        $config = array(
            array(
                'design' => '',
            ),
        );

        $this->assertConfigurationIsInvalid($config, 'The path "netgen_block_manager.design" cannot contain an empty value, but got "".');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\DesignNode::getConfigurationNode
     */
    public function testDesignSettingsWithInvalidDesignName()
    {
        $config = array(
            array(
                'design' => array(),
            ),
        );

        $this->assertConfigurationIsInvalid($config, 'Invalid type for path "netgen_block_manager.design". Expected scalar, but got array.');
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
