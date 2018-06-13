<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ExtensionPlugin;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;

final class AppNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode\AppNode::getConfigurationNode
     */
    public function testJavascripts()
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
    public function testJavascriptsWithNoJavascripts()
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
    public function testJavascriptsWithInvalidJavascripts()
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
    public function testJavascriptsWithInvalidJavascript()
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
    public function testStylesheets()
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
    public function testStylesheetsWithNoStylesheets()
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
    public function testStylesheetsWithInvalidStylesheets()
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
    public function testStylesheetsWithInvalidStylesheet()
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

    /**
     * Return the instance of ConfigurationInterface that should be used by the
     * Configuration-specific assertions in this test-case.
     *
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    protected function getConfiguration()
    {
        $extension = new NetgenBlockManagerExtension();
        $extension->addPlugin(new ExtensionPlugin());

        return new Configuration($extension);
    }
}
