<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ExtensionPlugin;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class AppNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode\AppNode::getConfigurationNode
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
            'app.javascripts'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode\AppNode::getConfigurationNode
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
            'app.javascripts'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode\AppNode::getConfigurationNode
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
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode\AppNode::getConfigurationNode
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
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode\AppNode::getConfigurationNode
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
            'app.stylesheets'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode\AppNode::getConfigurationNode
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
            'app.stylesheets'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode\AppNode::getConfigurationNode
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
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode\AppNode::getConfigurationNode
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
        $extension = new NetgenBlockManagerExtension();
        $extension->addPlugin(new ExtensionPlugin());

        return new Configuration($extension);
    }
}
