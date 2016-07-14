<?php

namespace Netgen\BlockManager\Tests\Collection\QueryType\Configuration;

use Netgen\BlockManager\Collection\QueryType\Configuration\Configuration;
use Netgen\BlockManager\Collection\QueryType\Configuration\Form;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration
     */
    protected $configuration;

    public function setUp()
    {
        $this->configuration = new Configuration(
            'query_type',
            'Query type',
            array(
                'full' => new Form('full', 'form_type', true, array('param1', 'param2')),
            ),
            array(
                'parameters' => array('parent_location_id' => 2),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration::__construct
     * @covers \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration::getName
     */
    public function testGetName()
    {
        self::assertEquals('Query type', $this->configuration->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration::getForms
     */
    public function testGetForms()
    {
        self::assertEquals(
            array(
                'full' => new Form('full', 'form_type', true, array('param1', 'param2')),
            ),
            $this->configuration->getForms()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration::hasForm
     */
    public function testHasForm()
    {
        self::assertTrue($this->configuration->hasForm('full'));
        self::assertFalse($this->configuration->hasForm('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration::getForm
     */
    public function testGetForm()
    {
        self::assertEquals(
            new Form('full', 'form_type', true, array('param1', 'param2')),
            $this->configuration->getForm('full')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration::getForm
     * @expectedException \RuntimeException
     */
    public function testGetFormThrowsRuntimeException()
    {
        $this->configuration->getForm('unknown');
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration::getDefaults
     */
    public function testGetDefaults()
    {
        self::assertEquals(
            array(
                'parameters' => array('parent_location_id' => 2),
            ),
            $this->configuration->getDefaults()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration::getDefaultParameters
     */
    public function testGetDefaultParameters()
    {
        self::assertEquals(array('parent_location_id' => 2), $this->configuration->getDefaultParameters());
    }
}
