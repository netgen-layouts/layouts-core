<?php

namespace Netgen\BlockManager\Tests\Serializer\Values;

use Netgen\BlockManager\Serializer\Values\EditView;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\ViewInterface;
use Symfony\Component\HttpFoundation\Response;

class EditViewTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\Values\EditView
     */
    protected $value;

    public function setUp()
    {
        $this->value = new EditView(new Value(), 42, Response::HTTP_ACCEPTED);
    }

    /**
     * @covers Netgen\BlockManager\Serializer\Values\EditView::getContext
     */
    public function testGetContext()
    {
        self::assertEquals(ViewInterface::CONTEXT_API_EDIT, $this->value->getContext());
    }
}
