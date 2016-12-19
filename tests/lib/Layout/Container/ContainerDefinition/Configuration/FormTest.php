<?php

namespace Netgen\BlockManager\Tests\Layout\Container\ContainerDefinition\Configuration;

use Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Form;
use PHPUnit\Framework\TestCase;

class FormTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Form
     */
    protected $form;

    public function setUp()
    {
        $this->form = new Form(array('identifier' => 'full', 'type' => 'form_type'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Form::__construct
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Form::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('full', $this->form->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Form::getType
     */
    public function testGetType()
    {
        $this->assertEquals('form_type', $this->form->getType());
    }
}
