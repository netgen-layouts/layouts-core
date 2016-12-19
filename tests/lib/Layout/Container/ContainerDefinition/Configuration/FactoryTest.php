<?php

namespace Netgen\BlockManager\Tests\Layout\Container\ContainerDefinition\Configuration;

use Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Configuration;
use Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Factory;
use Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Form;
use Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\ViewType;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Factory
     */
    protected $factory;

    public function setUp()
    {
        $this->factory = new Factory();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Factory::buildConfig
     */
    public function testBuildConfig()
    {
        $config = array(
            'name' => 'Container definition',
            'forms' => array(
                'full' => array(
                    'type' => 'container_form_type',
                    'enabled' => true,
                ),
                'other' => array(
                    'type' => 'type',
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
                'view_1' => array(
                    'name' => 'View 1',
                    'enabled' => true,
                ),
                'view_2' => array(
                    'name' => 'View 2',
                    'enabled' => false,
                ),
            ),
        );

        $containerDefinition = $this->factory->buildConfig(
            'container_definition',
            $config
        );

        $this->assertEquals(
            new Configuration(
                array(
                    'identifier' => 'container_definition',
                    'name' => 'Container definition',
                    'forms' => array(
                        'full' => new Form(
                            array(
                                'identifier' => 'full',
                                'type' => 'container_form_type',
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
                        'view_1' => new ViewType(
                            array(
                                'identifier' => 'view_1',
                                'name' => 'View 1',
                            )
                        ),
                    ),
                )
            ),
            $containerDefinition
        );
    }
}
