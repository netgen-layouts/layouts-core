<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ExtensionPlugin;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;

final class AdminNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode\AdminNode::getConfigurationNode
     */
    public function testJavascripts()
    {
        $config = [
            [
                'admin' => [
                    'javascripts' => [
                        'script',
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'admin' => [
                'javascripts' => [
                    'script',
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'admin.javascripts'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode\AdminNode::getConfigurationNode
     */
    public function testJavascriptsWithNoJavascripts()
    {
        $config = [
            [
                'admin' => [],
            ],
        ];

        $expectedConfig = [
            'admin' => [
                'javascripts' => [],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'admin.javascripts'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode\AdminNode::getConfigurationNode
     */
    public function testJavascriptsWithInvalidJavascripts()
    {
        $config = [
            [
                'admin' => [
                    'javascripts' => 'script',
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode\AdminNode::getConfigurationNode
     */
    public function testJavascriptsWithInvalidJavascript()
    {
        $config = [
            [
                'admin' => [
                    'javascripts' => [
                        42,
                    ],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid($config, 'The value should be a string');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode\AdminNode::getConfigurationNode
     */
    public function testStylesheets()
    {
        $config = [
            [
                'admin' => [
                    'stylesheets' => [
                        'script',
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'admin' => [
                'stylesheets' => [
                    'script',
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'admin.stylesheets'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode\AdminNode::getConfigurationNode
     */
    public function testStylesheetsWithNoStylesheets()
    {
        $config = [
            [
                'admin' => [],
            ],
        ];

        $expectedConfig = [
            'admin' => [
                'stylesheets' => [],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'admin.stylesheets'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode\AdminNode::getConfigurationNode
     */
    public function testStylesheetsWithInvalidStylesheets()
    {
        $config = [
            [
                'admin' => [
                    'stylesheets' => 'script',
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode\AdminNode::getConfigurationNode
     */
    public function testStylesheetsWithInvalidStylesheet()
    {
        $config = [
            [
                'admin' => [
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
