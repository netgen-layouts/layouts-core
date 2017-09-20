<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;

class ApiKeysNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ApiKeysNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testGoogleMapsApiKeySettings()
    {
        $config = array(
            array(
                'api_keys' => array(
                    'google_maps' => 'API_KEY',
                ),
            ),
        );

        $expectedConfig = array(
            'api_keys' => array(
                'google_maps' => 'API_KEY',
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'api_keys.google_maps'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ApiKeysNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testGoogleMapsApiKeySettingsWithEmptyKey()
    {
        $config = array();

        $expectedConfig = array(
            'api_keys' => array(
                'google_maps' => '',
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'api_keys.google_maps'
        );
    }

    /**
     * Return the instance of ConfigurationInterface that should be used by the
     * Configuration-specific assertions in this test-case.
     *
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    private function getConfiguration()
    {
        return new Configuration(new NetgenBlockManagerExtension());
    }
}
