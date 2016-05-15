<?php

namespace Netgen\BlockManager\Tests\Serializer\Values;

use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
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
     * @covers Netgen\BlockManager\Serializer\Values\FormView::setViewParameters
     * @covers Netgen\BlockManager\Serializer\Values\FormView::getViewParameters
     */
    public function testViewParameters()
    {
        $this->value->setViewParameters(array('param' => 'value'));
        self::assertEquals(array('param' => 'value'), $this->value->getViewParameters());
    }
}
