<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class DesignNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\DesignNode::getConfigurationNode
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
            'design'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\DesignNode::getConfigurationNode
     */
    public function testDesignSettingsWithEmptyDesignName(): void
    {
        $config = [
            [
                'design' => '',
            ],
        ];

        $this->assertConfigurationIsInvalid($config, 'The path "netgen_block_manager.design" cannot contain an empty value, but got "".');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\DesignNode::getConfigurationNode
     */
    public function testDesignSettingsWithInvalidDesignName(): void
    {
        $config = [
            [
                'design' => [],
            ],
        ];

        $this->assertConfigurationIsInvalid($config, 'Invalid type for path "netgen_block_manager.design". Expected scalar, but got array.');
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration(new NetgenBlockManagerExtension());
    }
}
