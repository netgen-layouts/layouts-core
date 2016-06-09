<?php

namespace Netgen\BlockManager\Tests\Traits;

use Netgen\BlockManager\Tests\Traits\Stubs\RequestStackAwareValue;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestStackAwareTraitTest extends \PHPUnit\Framework\TestCase
{
    public function testDefaultRequestStackValue()
    {
        $value = new RequestStackAwareValue();
        self::assertNull($value->getRequestStack());
    }

    /**
     * @covers \Netgen\BlockManager\Traits\RequestStackAwareTrait::setRequestStack
     */
    public function testSetRequestStack()
    {
        $requestStack = new RequestStack();

        $value = new RequestStackAwareValue();
        $value->setRequestStack($requestStack);

        self::assertEquals($requestStack, $value->getRequestStack());
    }
}
