<?php

namespace Netgen\BlockManager\Tests\Core\Service\StructBuilder;

use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Core\Service\StructBuilder\ConfigStructBuilder;
use Netgen\BlockManager\Core\Values\Config\Config;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinition;
use Netgen\BlockManager\Tests\Core\Service\ServiceTestCase;
use Netgen\BlockManager\Tests\Core\Stubs\ConfigAwareValue;

abstract class ConfigStructBuilderTest extends ServiceTestCase
{
    /**
     * @var \Netgen\BlockManager\Core\Service\StructBuilder\ConfigStructBuilder
     */
    private $structBuilder;

    public function setUp()
    {
        parent::setUp();

        $this->structBuilder = new ConfigStructBuilder();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\ConfigStructBuilder::buildConfigUpdateStructs
     */
    public function testBuildConfigUpdateStructs()
    {
        $block = new ConfigAwareValue(
            array(
                'configs' => array(
                    'config' => new Config(
                        array(
                            'definition' => new ConfigDefinition('config'),
                        )
                    ),
                ),
            )
        );

        $struct = new BlockUpdateStruct();

        $this->structBuilder->buildConfigUpdateStructs($block, $struct);

        $this->assertEquals(
            array(
                'config' => new ConfigStruct(
                    array(
                        'parameterValues' => array(
                            'param' => null,
                            'param2' => null,
                        ),
                    )
                ),
            ),
            $struct->getConfigStructs()
        );
    }
}
