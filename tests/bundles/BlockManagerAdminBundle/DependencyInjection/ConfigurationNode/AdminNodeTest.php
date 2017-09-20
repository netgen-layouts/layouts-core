<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ExtensionPlugin;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;

class AdminNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode\AdminNode::getConfigurationNode
     */
    public function testJavascripts()
    {
        $config = array(
            array(
                'admin' => array(
                    'javascripts' => array(
                        'script',
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'admin' => array(
                'javascripts' => array(
                    'script',
                ),
            ),
        );

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
        $config = array(
            array(
                'admin' => array(),
            ),
        );

        $expectedConfig = array(
            'admin' => array(
                'javascripts' => array(),
            ),
        );

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
        $config = array(
            array(
                'admin' => array(
                    'javascripts' => 'script',
                ),
            ),
        );

        $this->assertConfigurationIsInvalid($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode\AdminNode::getConfigurationNode
     */
    public function testJavascriptsWithInvalidJavascript()
    {
        $config = array(
            array(
                'admin' => array(
                    'javascripts' => array(
                        42,
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid($config, 'The value should be a string');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode\AdminNode::getConfigurationNode
     */
    public function testStylesheets()
    {
        $config = array(
            array(
                'admin' => array(
                    'stylesheets' => array(
                        'script',
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'admin' => array(
                'stylesheets' => array(
                    'script',
                ),
            ),
        );

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
        $config = array(
            array(
                'admin' => array(),
            ),
        );

        $expectedConfig = array(
            'admin' => array(
                'stylesheets' => array(),
            ),
        );

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
        $config = array(
            array(
                'admin' => array(
                    'stylesheets' => 'script',
                ),
            ),
        );

        $this->assertConfigurationIsInvalid($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode\AdminNode::getConfigurationNode
     */
    public function testStylesheetsWithInvalidStylesheet()
    {
        $config = array(
            array(
                'admin' => array(
                    'stylesheets' => array(
                        42,
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid($config, 'The value should be a string');
    }

    /**
     * Return the instance of ConfigurationInterface that should be used by the
     * Configuration-specific assertions in this test-case.
     *
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    private function getConfiguration()
    {
        $extension = new NetgenBlockManagerExtension();
        $extension->addPlugin(new ExtensionPlugin());

        return new Configuration($extension);
    }
}
