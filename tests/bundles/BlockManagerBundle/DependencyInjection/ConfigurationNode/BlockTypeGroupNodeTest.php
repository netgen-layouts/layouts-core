<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;

final class BlockTypeGroupNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeGroupNode::getConfigurationNode
     */
    public function testBlockTypeGroupsSettings()
    {
        $config = array(
            array(
                'block_type_groups' => array(
                    'block_type_group' => array(
                        'name' => 'block_type_group',
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_type_groups' => array(
                'block_type_group' => array(
                    'name' => 'block_type_group',
                    'enabled' => true,
                    'block_types' => array(),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_type_groups'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeGroupNode::getConfigurationNode
     */
    public function testBlockTypeGroupsSettingsWithBlockTypesMerge()
    {
        $config = array(
            array(
                'block_type_groups' => array(
                    'block_type_group' => array(
                        'block_types' => array('title', 'text'),
                    ),
                ),
            ),
            array(
                'block_type_groups' => array(
                    'block_type_group' => array(
                        'block_types' => array('image'),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_type_groups' => array(
                'block_type_group' => array(
                    'block_types' => array('title', 'text', 'image'),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_type_groups.*.block_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeGroupNode::getConfigurationNode
     */
    public function testBlockTypeGroupsSettingsWithBlockTypes()
    {
        $config = array(
            array(
                'block_type_groups' => array(
                    'block_type_group' => array(
                        'block_types' => array('title', 'image'),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_type_groups' => array(
                'block_type_group' => array(
                    'block_types' => array('title', 'image'),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_type_groups.*.block_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeGroupNode::getConfigurationNode
     */
    public function testBlockTypeGroupsSettingsWithNonUniqueBlockTypes()
    {
        $config = array(
            array(
                'block_type_groups' => array(
                    'block_type_group' => array(
                        'block_types' => array('title', 'image', 'title'),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_type_groups' => array(
                'block_type_group' => array(
                    'block_types' => array('title', 'image'),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_type_groups.*.block_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeGroupNode::getConfigurationNode
     */
    public function testBlockTypeGroupsSettingsWithNoBlockTypeSettings()
    {
        $config = array(
            'block_type_groups' => array(),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeGroupNode::getConfigurationNode
     */
    public function testBlockTypeGroupsSettingsWithNoName()
    {
        $config = array(
            'block_type_groups' => array(
                'block_type_group' => array(),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeGroupNode::getConfigurationNode
     */
    public function testBlockTypeGroupsSettingsWithEmptyBlockTypes()
    {
        $config = array(
            'block_type_groups' => array(
                'block_type_group' => array(
                    'name' => 'block_type_group',
                    'block_types' => array(),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * Return the instance of ConfigurationInterface that should be used by the
     * Configuration-specific assertions in this test-case.
     *
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    private function getConfiguration()
    {
        return new Configuration(new NetgenBlockManagerExtension());
    }
}
