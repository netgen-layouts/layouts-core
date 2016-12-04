<?php

namespace Netgen\BlockManager\Tests\Traits;

use Netgen\BlockManager\Tests\Traits\Stubs\RequestStackAwareValue;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestStackAwareTraitTest extends TestCase
{
    public function testDefaultRequestStackValue()
    {
        $value = new RequestStackAwareValue();
        $this->assertNull($value->getRequestStack());
    }

    /**
     * @covers \Netgen\BlockManager\Traits\RequestStackAwareTrait::setRequestStack
     */
    public function testSetRequestStack()
    {
        $requestStack = new RequestStack();

        $value = new RequestStackAwareValue();
        $value->setRequestStack($requestStack);

        $this->assertEquals($requestStack, $value->getRequestStack());
    }
}
