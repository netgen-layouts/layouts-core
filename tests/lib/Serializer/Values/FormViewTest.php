<?php

namespace Netgen\BlockManager\Tests\Serializer\Values;

use Netgen\BlockManager\Serializer\Values\FormView;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\ViewInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

final class FormViewTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\Values\FormView
     */
    private $value;

    public function setUp()
    {
        $this->value = new FormView(new Value(), 42, Response::HTTP_ACCEPTED);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Values\View::getViewParameters
     * @covers \Netgen\BlockManager\Serializer\Values\View::setViewParameters
     */
    public function testViewParameters()
    {
        $this->value->setViewParameters(array('param' => 'value'));
        $this->assertEquals(array('param' => 'value'), $this->value->getViewParameters());
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Values\AbstractView::getContext
     */
    public function testGetContext()
    {
        $this->assertEquals(ViewInterface::CONTEXT_API, $this->value->getContext());
    }
}
