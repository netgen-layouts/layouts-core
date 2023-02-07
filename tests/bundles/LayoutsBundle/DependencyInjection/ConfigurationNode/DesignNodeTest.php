<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class DesignNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\DesignNode::getConfigurationNode
     */
    public function testDesignSettings(): void
    {
        $config = [
            [],
        ];

        $expectedConfig = [
            'design' => 'standard',
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'design',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\DesignNode::getConfigurationNode
     */
    public function testDesignSettingsWithEmptyDesignName(): void
    {
        $config = [
            [
                'design' => '',
            ],
        ];

        $this->assertConfigurationIsInvalid($config, 'The path "netgen_layouts.design" cannot contain an empty value, but got "".');
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\DesignNode::getConfigurationNode
     */
    public function testDesignSettingsWithInvalidDesignName(): void
    {
        $config = [
            [
                'design' => [],
            ],
        ];

        $this->assertConfigurationIsInvalid(
            $config,
            '/Invalid type for path "netgen_layouts.design". Expected "?scalar"?, but got "?array"?./',
            true,
        );
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration(new NetgenLayoutsExtension());
    }
}
