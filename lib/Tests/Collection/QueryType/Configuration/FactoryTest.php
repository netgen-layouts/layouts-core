<?php

namespace Netgen\BlockManager\Tests\Collection\QueryType\Configuration;

use Netgen\BlockManager\Collection\QueryType\Configuration\Configuration;
use Netgen\BlockManager\Collection\QueryType\Configuration\Factory;
use Netgen\BlockManager\Collection\QueryType\Configuration\Form;

class FactoryTest extends \PHPUnit_Framework_TestCase
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
     * @covers \Netgen\BlockManager\Collection\QueryType\Configuration\Factory::buildQueryTypeConfig
     */
    public function testBuildQueryTypeConfig()
    {
        $config = array(
            'forms' => array(
                'full' => array(
                    'type' => 'form_type',
                    'parameters' => array('param1', 'param2'),
                ),
            ),
        );

        $queryType = $this->factory->buildQueryTypeConfig(
            'query_type',
            $config
        );

        self::assertEquals(
            new Configuration(
                'query_type',
                array(
                    'full' => new Form('full', 'form_type', array('param1', 'param2')),
                )
            ),
            $queryType
        );
    }
}
