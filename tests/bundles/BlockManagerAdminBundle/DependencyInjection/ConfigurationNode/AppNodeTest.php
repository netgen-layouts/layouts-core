<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ExtensionPlugin;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;

class AppNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode\AppNode::getConfigurationNode
     */
    public function testJavascripts()
    {
        $config = array(
            array(
                'app' => array(
                    'javascripts' => array(
                        'script',
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'app' => array(
                'javascripts' => array(
                    'script',
                ),
            ),
        );

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
        $config = array(
            array(
                'app' => array(),
            ),
        );

        $expectedConfig = array(
            'app' => array(
                'javascripts' => array(),
            ),
        );

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
        $config = array(
            array(
                'app' => array(
                    'javascripts' => 'script',
                ),
            ),
        );

        $this->assertConfigurationIsInvalid($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode\AppNode::getConfigurationNode
     */
    public function testJavascriptsWithInvalidJavascript()
    {
        $config = array(
            array(
                'app' => array(
                    'javascripts' => array(
                        42,
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid($config, 'The value should be a string');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode\AppNode::getConfigurationNode
     */
    public function testStylesheets()
    {
        $config = array(
            array(
                'app' => array(
                    'stylesheets' => array(
                        'script',
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'app' => array(
                'stylesheets' => array(
                    'script',
                ),
            ),
        );

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
        $config = array(
            array(
                'app' => array(),
            ),
        );

        $expectedConfig = array(
            'app' => array(
                'stylesheets' => array(),
            ),
        );

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
        $config = array(
            array(
                'app' => array(
                    'stylesheets' => 'script',
                ),
            ),
        );

        $this->assertConfigurationIsInvalid($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode\AppNode::getConfigurationNode
     */
    public function testStylesheetsWithInvalidStylesheet()
    {
        $config = array(
            array(
                'app' => array(
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
    protected function getConfiguration()
    {
        $extension = new NetgenBlockManagerExtension();
        $extension->addPlugin(new ExtensionPlugin());

        return new Configuration($extension);
    }
}
