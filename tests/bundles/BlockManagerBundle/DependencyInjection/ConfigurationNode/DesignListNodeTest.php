<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class DesignListNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\DesignListNode::getConfigurationNode
     */
    public function testDesignListSettings(): void
    {
        $config = [
            [
                'design_list' => [
                    'design1' => [
                        'theme1',
                        'theme2',
                    ],
                    'design2' => [
                        'theme2',
                        'theme3',
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'design_list' => [
                'design1' => [
                    'theme1',
                    'theme2',
                ],
                'design2' => [
                    'theme2',
                    'theme3',
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'design_list'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\DesignListNode::getConfigurationNode
     */
    public function testDesignListSettingsWithEmptyDesignList(): void
    {
        $config = [
            'design_list' => [],
        ];

        $expectedConfig = [
            'design_list' => [],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'design_list'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\DesignListNode::getConfigurationNode
     */
    public function testDesignListSettingsWithEmptyDesign(): void
    {
        $config = [
            [
                'design_list' => [
                    'design' => [],
                ],
            ],
        ];

        $expectedConfig = [
            'design_list' => [
                'design' => [],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'design_list'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\DesignListNode::getConfigurationNode
     */
    public function testDesignListSettingsWithEmptyThemeName(): void
    {
        $config = [
            [
                'design_list' => [
                    'design' => [
                        '',
                    ],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid($config, 'The path "netgen_block_manager.design_list.design.0" cannot contain an empty value, but got "".');
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration(new NetgenBlockManagerExtension());
    }
}
