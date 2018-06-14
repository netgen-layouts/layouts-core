<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class ApiKeysNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ApiKeysNode::getConfigurationNode
     */
    public function testGoogleMapsApiKeySettings(): void
    {
        $config = [
            [
                'api_keys' => [
                    'google_maps' => 'API_KEY',
                ],
            ],
        ];

        $expectedConfig = [
            'api_keys' => [
                'google_maps' => 'API_KEY',
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'api_keys.google_maps'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ApiKeysNode::getConfigurationNode
     */
    public function testGoogleMapsApiKeySettingsWithEmptyKey(): void
    {
        $config = [];

        $expectedConfig = [
            'api_keys' => [
                'google_maps' => '',
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'api_keys.google_maps'
        );
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration(new NetgenBlockManagerExtension());
    }
}
