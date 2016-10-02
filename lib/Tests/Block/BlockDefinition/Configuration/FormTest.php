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
        $this->form = new Form('content', 'form_type', true);
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
}
