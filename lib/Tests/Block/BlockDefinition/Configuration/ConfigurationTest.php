<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Configuration;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Form;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration
     */
    protected $configuration;

    public function setUp()
    {
        $this->configuration = new Configuration(
            'block_definition',
            array(
                'content' => new Form('content', 'form_type', true, array('param1', 'param2')),
            ),
            array(
                'large' => new ViewType('large', 'Large'),
                'small' => new ViewType('small', 'Small'),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration::__construct
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration::getForms
     */
    public function testGetForms()
    {
        self::assertEquals(
            array(
                'content' => new Form('content', 'form_type', true, array('param1', 'param2')),
            ),
            $this->configuration->getForms()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration::hasForm
     */
    public function testHasForm()
    {
        self::assertTrue($this->configuration->hasForm('content'));
        self::assertFalse($this->configuration->hasForm('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration::getForm
     */
    public function testGetForm()
    {
        self::assertEquals(
            new Form('content', 'form_type', true, array('param1', 'param2')),
            $this->configuration->getForm('content')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration::getForm
     * @expectedException \RuntimeException
     */
    public function testGetFormThrowsRuntimeException()
    {
        $this->configuration->getForm('unknown');
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration::getViewTypes
     */
    public function testGetViewTypes()
    {
        self::assertEquals(
            array(
                'large' => new ViewType('large', 'Large'),
                'small' => new ViewType('small', 'Small'),
            ),
            $this->configuration->getViewTypes()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration::hasViewType
     */
    public function testHasViewType()
    {
        self::assertTrue($this->configuration->hasViewType('large'));
        self::assertFalse($this->configuration->hasViewType('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration::getViewType
     */
    public function testGetViewType()
    {
        self::assertEquals(
            new ViewType('large', 'Large'),
            $this->configuration->getViewType('large')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration::getViewType
     * @expectedException \RuntimeException
     */
    public function testGetViewTypeThrowsRuntimeException()
    {
        $this->configuration->getViewType('unknown');
    }
}
