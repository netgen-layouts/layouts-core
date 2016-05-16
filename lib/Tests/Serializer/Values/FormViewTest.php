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
        $this->value = new FormView(
            $this->getMock(FormInterface::class),
            new Value(),
            42,
            Response::HTTP_ACCEPTED
        );
    }

    /**
     * @covers Netgen\BlockManager\Serializer\Values\FormView::__construct
     * @covers Netgen\BlockManager\Serializer\Values\FormView::getForm
     */
    public function testGetForm()
    {
        self::assertInstanceOf(FormInterface::class, $this->value->getForm());
    }
}
