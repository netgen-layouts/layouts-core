<?php

namespace Netgen\BlockManager\Tests\Serializer\Values;

use Netgen\BlockManager\Serializer\Values\FormView;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

class FormViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\Values\FormView
     */
    protected $value;

    public function setUp()
    {
        $this->value = new FormView(new Value(), 42, Response::HTTP_ACCEPTED);
    }

    /**
     * @covers Netgen\BlockManager\Serializer\Values\FormView::setForm
     * @covers Netgen\BlockManager\Serializer\Values\FormView::getForm
     */
    public function testForm()
    {
        $form = $this->getMock(FormInterface::class);
        $this->value->setForm($form);
        self::assertEquals($form, $this->value->getForm());
    }
}
