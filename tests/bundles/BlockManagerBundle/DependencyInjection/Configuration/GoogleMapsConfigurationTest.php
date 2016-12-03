<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\Configuration;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;

class GoogleMapsConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

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

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\GoogleMapsNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testGoogleMapsApiKeySettings()
    {
        $config = array(
            array(
                'google_maps_api_key' => 'API_KEY',
            ),
        );

        $expectedConfig = array(
            'google_maps_api_key' => 'API_KEY',
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'google_maps_api_key'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\GoogleMapsNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testGoogleMapsApiKeySettingsWithNoKey()
    {
        $config = array();

        $expectedConfig = array(
            'google_maps_api_key' => '',
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'google_maps_api_key'
        );
    }
}
