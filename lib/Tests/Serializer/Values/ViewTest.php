<?php

namespace Netgen\BlockManager\Tests\Serializer\Values;

use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\ViewInterface;
use Symfony\Component\HttpFoundation\Response;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\Values\View
     */
    protected $value;

    public function setUp()
    {
        $this->value = new View(new Value(), 42, Response::HTTP_ACCEPTED);
    }

    /**
     * @covers Netgen\BlockManager\Serializer\Values\View::setViewParameters
     * @covers Netgen\BlockManager\Serializer\Values\View::getViewParameters
     */
    public function testViewParameters()
    {
        $this->value->setViewParameters(array('param' => 'value'));
        self::assertEquals(array('param' => 'value'), $this->value->getViewParameters());
    }

    /**
     * @covers Netgen\BlockManager\Serializer\Values\View::getContext
     */
    public function testGetContext()
    {
        self::assertEquals(ViewInterface::CONTEXT_API_VIEW, $this->value->getContext());
    }
}
