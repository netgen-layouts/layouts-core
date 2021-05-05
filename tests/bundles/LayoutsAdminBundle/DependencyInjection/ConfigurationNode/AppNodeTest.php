<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\LayoutsAdminBundle\DependencyInjection\ExtensionPlugin;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class AppNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\DependencyInjection\ConfigurationNode\AppNode::getConfigurationNode
     */
    public function testJavascripts(): void
    {
        $config = [
            [
                'app' => [
                    'javascripts' => [
                        'script',
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'app' => [
                'javascripts' => [
                    'script',
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'app.javascripts',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\DependencyInjection\ConfigurationNode\AppNode::getConfigurationNode
     */
    public function testJavascriptsWithNoJavascripts(): void
    {
        $config = [
            [
                'app' => [],
            ],
        ];

        $expectedConfig = [
            'app' => [
                'javascripts' => [],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'app.javascripts',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\DependencyInjection\ConfigurationNode\AppNode::getConfigurationNode
     */
    public function testJavascriptsWithEmptyJavascripts(): void
    {
        $config = [
            [
                'app' => [
                    'javascripts' => [],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid($config, 'The path "netgen_layouts.app.javascripts" should have at least 1 element(s) defined.');
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\DependencyInjection\ConfigurationNode\AppNode::getConfigurationNode
     */
    public function testJavascriptsWithInvalidJavascripts(): void
    {
        $config = [
            [
                'app' => [
                    'javascripts' => 'script',
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid($config);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\DependencyInjection\ConfigurationNode\AppNode::getConfigurationNode
     */
    public function testJavascriptsWithInvalidJavascript(): void
    {
        $config = [
            [
                'app' => [
                    'javascripts' => [
                        42,
                    ],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid($config, 'The value should be a string');
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\DependencyInjection\ConfigurationNode\AppNode::getConfigurationNode
     */
    public function testStylesheets(): void
    {
        $config = [
            [
                'app' => [
                    'stylesheets' => [
                        'script',
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'app' => [
                'stylesheets' => [
                    'script',
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'app.stylesheets',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\DependencyInjection\ConfigurationNode\AppNode::getConfigurationNode
     */
    public function testStylesheetsWithNoStylesheets(): void
    {
        $config = [
            [
                'app' => [],
            ],
        ];

        $expectedConfig = [
            'app' => [
                'stylesheets' => [],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'app.stylesheets',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\DependencyInjection\ConfigurationNode\AppNode::getConfigurationNode
     */
    public function testStylesheetsWithEmptyStylesheets(): void
    {
        $config = [
            [
                'app' => [
                    'stylesheets' => [],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid($config, 'The path "netgen_layouts.app.stylesheets" should have at least 1 element(s) defined.');
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\DependencyInjection\ConfigurationNode\AppNode::getConfigurationNode
     */
    public function testStylesheetsWithInvalidStylesheets(): void
    {
        $config = [
            [
                'app' => [
                    'stylesheets' => 'script',
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid($config);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\DependencyInjection\ConfigurationNode\AppNode::getConfigurationNode
     */
    public function testStylesheetsWithInvalidStylesheet(): void
    {
        $config = [
            [
                'app' => [
                    'stylesheets' => [
                        42,
                    ],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid($config, 'The value should be a string');
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        $extension = new NetgenLayoutsExtension();
        $extension->addPlugin(new ExtensionPlugin());

        return new Configuration($extension);
    }
}
