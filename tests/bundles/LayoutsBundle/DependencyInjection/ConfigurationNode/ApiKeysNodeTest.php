<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\ApiKeysNode;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

#[CoversClass(ApiKeysNode::class)]
#[CoversClass(Configuration::class)]
final class ApiKeysNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

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
            'api_keys.google_maps',
        );
    }

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
            'api_keys.google_maps',
        );
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration(new NetgenLayoutsExtension());
    }
}
