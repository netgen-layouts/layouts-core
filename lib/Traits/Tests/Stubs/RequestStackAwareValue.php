<?php

namespace Netgen\BlockManager\Traits\Tests\Stubs;

use Netgen\BlockManager\Traits\RequestStackAwareTrait;

class RequestStackAwareValue
{
    use RequestStackAwareTrait;

    /**
     * Returns the request stack.
     *
     * @return \Symfony\Component\HttpFoundation\RequestStack
     */
    public function getRequestStack()
    {
        return $this->requestStack;
    }
}
