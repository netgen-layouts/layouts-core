<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;

final class DesignListNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\DesignListNode::getConfigurationNode
     */
    public function testDesignListSettings()
    {
        $config = array(
            array(
                'design_list' => array(
                    'design1' => array(
                        'theme1',
                        'theme2',
                    ),
                    'design2' => array(
                        'theme2',
                        'theme3',
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'design_list' => array(
                'design1' => array(
                    'theme1',
                    'theme2',
                ),
                'design2' => array(
                    'theme2',
                    'theme3',
                ),
            ),
        );

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
    public function testDesignListSettingsWithEmptyDesignList()
    {
        $config = array(
            'design_list' => array(),
        );

        $expectedConfig = array(
            'design_list' => array(),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'design_list'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\DesignListNode::getConfigurationNode
     */
    public function testDesignListSettingsWithEmptyDesign()
    {
        $config = array(
            array(
                'design_list' => array(
                    'design' => array(),
                ),
            ),
        );

        $expectedConfig = array(
            'design_list' => array(
                'design' => array(),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'design_list'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\DesignListNode::getConfigurationNode
     */
    public function testDesignListSettingsWithEmptyThemeName()
    {
        $config = array(
            array(
                'design_list' => array(
                    'design' => array(
                        '',
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid($config, 'The path "netgen_block_manager.design_list.design.0" cannot contain an empty value, but got "".');
    }

    /**
     * Return the instance of ConfigurationInterface that should be used by the
     * Configuration-specific assertions in this test-case.
     *
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    protected function getConfiguration()
    {
        return new Configuration(new NetgenBlockManagerExtension());
    }
}
