<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Configuration;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\Form;
use PHPUnit\Framework\TestCase;

class FormTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\Form
     */
    protected $form;

    public function setUp()
    {
        $this->form = new Form('content', 'form_type', true, array('param1', 'param2'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Form::__construct
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Form::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('content', $this->form->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Form::getType
     */
    public function testGetType()
    {
        $this->assertEquals('form_type', $this->form->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Form::isEnabled
     */
    public function testIsEnabled()
    {
        $this->assertTrue($this->form->isEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Form::getParameters
     */
    public function testGetParameters()
    {
        $this->assertEquals(array('param1', 'param2'), $this->form->getParameters());
    }
}
