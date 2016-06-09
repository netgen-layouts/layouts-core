<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\Configuration;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;

class BlockTypeGroupsConfigurationTest extends \PHPUnit\Framework\TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * Return the instance of ConfigurationInterface that should be used by the
     * Configuration-specific assertions in this test-case.
     *
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    protected function getConfiguration()
    {
        $extension = new NetgenBlockManagerExtension();

        return new Configuration($extension->getAlias());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypeGroupsNodeDefinition
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypeGroupsNodeDefinition
     */
    public function testBlockTypeGroupsSettingsWithBlockTypesMerge()
    {
        $config = array(
            array(
                'block_type_groups' => array(
                    'block_type_group' => array(
                        'name' => 'block_type_group',
                        'block_types' => array('title', 'paragraph'),
                    ),
                ),
            ),
            array(
                'block_type_groups' => array(
                    'block_type_group' => array(
                        'name' => 'block_type_group',
                        'block_types' => array('image'),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_type_groups' => array(
                'block_type_group' => array(
                    'name' => 'block_type_group',
                    'enabled' => true,
                    'block_types' => array('title', 'paragraph', 'image'),
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypeGroupsNodeDefinition
     */
    public function testBlockTypeGroupsSettingsWithBlockTypes()
    {
        $config = array(
            array(
                'block_type_groups' => array(
                    'block_type_group' => array(
                        'name' => 'block_type_group',
                        'block_types' => array('title', 'image'),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_type_groups' => array(
                'block_type_group' => array(
                    'name' => 'block_type_group',
                    'enabled' => true,
                    'block_types' => array('title', 'image'),
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypeGroupsNodeDefinition
     */
    public function testBlockTypeGroupsSettingsWithNonUniqueBlockTypes()
    {
        $config = array(
            array(
                'block_type_groups' => array(
                    'block_type_group' => array(
                        'name' => 'block_type_group',
                        'block_types' => array('title', 'image', 'title'),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_type_groups' => array(
                'block_type_group' => array(
                    'name' => 'block_type_group',
                    'enabled' => true,
                    'block_types' => array('title', 'image'),
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypeGroupsNodeDefinition
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypeGroupsNodeDefinition
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockTypeGroupsNodeDefinition
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
}
