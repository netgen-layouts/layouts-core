<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Configuration;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Factory;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Form;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\Factory
     */
    protected $factory;

    public function setUp()
    {
        $this->factory = new Factory();
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Factory::buildConfig
     */
    public function testBuildConfig()
    {
        $config = array(
            'name' => 'Block definition',
            'forms' => array(
                'content' => array(
                    'type' => 'form_type',
                    'enabled' => true,
                ),
                'full' => array(
                    'type' => 'form_type',
                    'enabled' => false,
                ),
            ),
            'placeholder_forms' => array(
                'full' => array(
                    'type' => 'placeholder_form_type',
                    'enabled' => true,
                ),
                'other' => array(
                    'type' => 'type',
                    'enabled' => false,
                ),
            ),
            'view_types' => array(
                'large' => array(
                    'name' => 'Large',
                    'enabled' => true,
                    'item_view_types' => array(
                        'standard' => array(
                            'enabled' => true,
                            'name' => 'Standard',
                        ),
                    ),
                    'valid_parameters' => array('param1', 'param2'),
                ),
                'medium' => array(
                    'name' => 'Medium',
                    'enabled' => true,
                    'item_view_types' => array(
                        'standard_with_intro' => array(
                            'enabled' => true,
                            'name' => 'Standard (with intro)',
                        ),
                    ),
                    'valid_parameters' => null,
                ),
                'medium2' => array(
                    'name' => 'Medium 2',
                    'enabled' => true,
                    'item_view_types' => array(
                        'standard' => array(
                            'enabled' => false,
                        ),
                        'standard_with_intro' => array(
                            'enabled' => true,
                            'name' => 'Standard (with intro)',
                        ),
                    ),
                    'valid_parameters' => null,
                ),
                'small' => array(
                    'name' => 'Small',
                    'enabled' => false,
                    'item_view_types' => array(
                        'standard' => array(
                            'enabled' => true,
                            'name' => 'Standard',
                        ),
                    ),
                ),
            ),
        );

        $blockDefinition = $this->factory->buildConfig(
            'block_definition',
            $config
        );

        $this->assertEquals(
            new Configuration(
                array(
                    'identifier' => 'block_definition',
                    'name' => 'Block definition',
                    'forms' => array(
                        'content' => new Form(
                            array(
                                'identifier' => 'content',
                                'type' => 'form_type',
                            )
                        ),
                    ),
                    'placeholderForms' => array(
                        'full' => new Form(
                            array(
                                'identifier' => 'full',
                                'type' => 'placeholder_form_type',
                            )
                        ),
                    ),
                    'viewTypes' => array(
                        'large' => new ViewType(
                            array(
                                'identifier' => 'large',
                                'name' => 'Large',
                                'itemViewTypes' => array(
                                    'standard' => new ItemViewType(
                                        array(
                                            'identifier' => 'standard',
                                            'name' => 'Standard',
                                        )
                                    ),
                                ),
                                'validParameters' => array('param1', 'param2'),
                            )
                        ),
                        'medium' => new ViewType(
                            array(
                                'identifier' => 'medium',
                                'name' => 'Medium',
                                'itemViewTypes' => array(
                                    'standard' => new ItemViewType(
                                        array(
                                            'identifier' => 'standard',
                                            'name' => 'Standard',
                                        )
                                    ),
                                    'standard_with_intro' => new ItemViewType(
                                        array(
                                            'identifier' => 'standard_with_intro',
                                            'name' => 'Standard (with intro)',
                                        )
                                    ),
                                ),
                            )
                        ),
                        'medium2' => new ViewType(
                            array(
                                'identifier' => 'medium2',
                                'name' => 'Medium 2',
                                'itemViewTypes' => array(
                                    'standard_with_intro' => new ItemViewType(
                                        array(
                                            'identifier' => 'standard_with_intro',
                                            'name' => 'Standard (with intro)',
                                        )
                                    ),
                                ),
                            )
                        ),
                    ),
                )
            ),
            $blockDefinition
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Factory::buildConfig
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage You need to specify at least one enabled view type for "block_definition" block definition.
     */
    public function testBuildConfigWithNoViewTypes()
    {
        $config = array(
            'name' => 'Block definition',
            'forms' => array(
                'full' => array(
                    'type' => 'form_type',
                    'enabled' => true,
                ),
            ),
            'placeholder_forms' => array(
                'full' => array(
                    'type' => 'placeholder_form_type',
                    'enabled' => true,
                ),
            ),
            'view_types' => array(
                'large' => array(
                    'enabled' => false,
                    'valid_parameters' => null,
                ),
            ),
        );

        $this->factory->buildConfig(
            'block_definition',
            $config
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Factory::buildConfig
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage You need to specify at least one enabled item view type for "large" view type and "block_definition" block definition.
     */
    public function testBuildConfigWithNoItemViewTypes()
    {
        $config = array(
            'name' => 'Block definition',
            'forms' => array(
                'full' => array(
                    'type' => 'form_type',
                    'enabled' => true,
                ),
            ),
            'placeholder_forms' => array(
                'full' => array(
                    'type' => 'placeholder_form_type',
                    'enabled' => true,
                ),
            ),
            'view_types' => array(
                'large' => array(
                    'name' => 'Large',
                    'enabled' => true,
                    'item_view_types' => array(
                        'standard' => array(
                            'enabled' => false,
                        ),
                    ),
                    'valid_parameters' => null,
                ),
            ),
        );

        $this->factory->buildConfig(
            'block_definition',
            $config
        );
    }
}
