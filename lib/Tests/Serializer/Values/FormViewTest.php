<?php

namespace Netgen\BlockManager\Tests\Serializer\Values;

use Netgen\BlockManager\Serializer\Values\FormView;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\ViewInterface;
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
            $this->createMock(FormInterface::class),
            'full',
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

    /**
     * @covers Netgen\BlockManager\Serializer\Values\FormView::__construct
     * @covers Netgen\BlockManager\Serializer\Values\FormView::getFormName
     */
    public function testGetFormName()
    {
        self::assertEquals('full', $this->value->getFormName());
    }

    /**
     * @covers Netgen\BlockManager\Serializer\Values\FormView::getContext
     */
    public function testGetContext()
    {
        self::assertEquals(ViewInterface::CONTEXT_API_FORM, $this->value->getContext());
    }
}
