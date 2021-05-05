<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class DesignListNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\DesignListNode::getConfigurationNode
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
            'design_list',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\DesignListNode::getConfigurationNode
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
            'design_list',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\DesignListNode::getConfigurationNode
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
            'design_list',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\DesignListNode::getConfigurationNode
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

        $this->assertConfigurationIsInvalid($config, 'The path "netgen_layouts.design_list.design.0" cannot contain an empty value, but got "".');
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration(new NetgenLayoutsExtension());
    }
}
