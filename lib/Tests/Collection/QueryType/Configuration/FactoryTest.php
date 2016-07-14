<?php

namespace Netgen\BlockManager\Tests\Collection\QueryType\Configuration;

use Netgen\BlockManager\Collection\QueryType\Configuration\Configuration;
use Netgen\BlockManager\Collection\QueryType\Configuration\Factory;
use Netgen\BlockManager\Collection\QueryType\Configuration\Form;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Collection\QueryType\Configuration\Factory
     */
    protected $factory;

    public function setUp()
    {
        $this->factory = new Factory();
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\Configuration\Factory::buildConfig
     */
    public function testBuildConfig()
    {
        $config = array(
            'name' => 'Query type',
            'forms' => array(
                'full' => array(
                    'type' => 'form_type',
                    'parameters' => array('param1', 'param2'),
                ),
            ),
            'defaults' => array(
                'parameters' => array(
                    'param' => 'value',
                ),
            ),
        );

        $queryType = $this->factory->buildConfig(
            'query_type',
            $config
        );

        self::assertEquals(
            new Configuration(
                'query_type',
                'Query type',
                array(
                    'full' => new Form('full', 'form_type', array('param1', 'param2')),
                ),
                array(
                    'parameters' => array(
                        'param' => 'value',
                    ),
                )
            ),
            $queryType
        );
    }
}
