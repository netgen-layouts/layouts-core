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
            array(
                'type' => 'query_type',
                'name' => 'Query type',
                'forms' => array(
                    'full' => new Form(
                        array(
                            'identifier' => 'full',
                            'type' => 'form_type',
                        )
                    ),
                ),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration::__construct
     * @covers \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration::getName
     */
    public function testGetName()
    {
        $this->assertEquals('Query type', $this->configuration->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration::getForms
     */
    public function testGetForms()
    {
        $this->assertEquals(
            array(
                'full' => new Form(array('identifier' => 'full', 'type' => 'form_type')),
            ),
            $this->configuration->getForms()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration::hasForm
     */
    public function testHasForm()
    {
        $this->assertTrue($this->configuration->hasForm('full'));
        $this->assertFalse($this->configuration->hasForm('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration::getForm
     */
    public function testGetForm()
    {
        $this->assertEquals(
            new Form(array('identifier' => 'full', 'type' => 'form_type')),
            $this->configuration->getForm('full')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration::getForm
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     */
    public function testGetFormThrowsInvalidArgumentException()
    {
        $this->configuration->getForm('unknown');
    }
}
