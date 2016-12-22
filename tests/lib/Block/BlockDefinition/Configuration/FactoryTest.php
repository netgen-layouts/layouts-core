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
                            'name' => 'Standard',
                        ),
                    ),
                    'valid_parameters' => array('param1', 'param2'),
                ),
                'small' => array(
                    'name' => 'Small',
                    'enabled' => false,
                    'item_view_types' => array(
                        'standard' => array(
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
                    ),
                )
            ),
            $blockDefinition
        );
    }
}
