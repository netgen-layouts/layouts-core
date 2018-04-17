<?php

namespace Netgen\BlockManager\Tests\Collection\QueryType\Configuration;

use Netgen\BlockManager\Collection\QueryType\Configuration\Form;
use PHPUnit\Framework\TestCase;

final class FormTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Collection\QueryType\Configuration\Form
     */
    private $form;

    public function setUp()
    {
        $this->form = new Form(['identifier' => 'full', 'type' => 'form_type']);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\Configuration\Form::__construct
     * @covers \Netgen\BlockManager\Collection\QueryType\Configuration\Form::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('full', $this->form->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\Configuration\Form::getType
     */
    public function testGetType()
    {
        $this->assertEquals('form_type', $this->form->getType());
    }
}
