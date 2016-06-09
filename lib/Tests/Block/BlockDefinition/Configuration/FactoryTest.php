<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Configuration;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Factory;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Form;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;

class FactoryTest extends \PHPUnit\Framework\TestCase
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
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Factory::buildBlockDefinitionConfig
     */
    public function testBuildBlockDefinitionConfig()
    {
        $config = array(
            'forms' => array(
                'content' => array(
                    'type' => 'form_type',
                    'parameters' => array('param1', 'param2'),
                ),
            ),
            'view_types' => array(
                'large' => array(
                    'name' => 'Large',
                    'item_view_types' => array(
                        'standard' => array(
                            'name' => 'Standard',
                        ),
                    ),
                ),
                'small' => array(
                    'name' => 'Small',
                    'item_view_types' => array(
                        'standard' => array(
                            'name' => 'Standard',
                        ),
                    ),
                ),
            ),
        );

        $blockDefinition = $this->factory->buildBlockDefinitionConfig(
            'block_definition',
            $config
        );

        self::assertEquals(
            new Configuration(
                'block_definition',
                array(
                    'content' => new Form('content', 'form_type', array('param1', 'param2')),
                ),
                array(
                    'large' => new ViewType(
                        'large',
                        'Large',
                        array(
                            'standard' => new ItemViewType(
                                'standard',
                                'Standard'
                            ),
                        )
                    ),
                    'small' => new ViewType(
                        'small',
                        'Small',
                        array(
                            'standard' => new ItemViewType(
                                'standard',
                                'Standard'
                            ),
                        )
                    ),
                )
            ),
            $blockDefinition
        );
    }
}
