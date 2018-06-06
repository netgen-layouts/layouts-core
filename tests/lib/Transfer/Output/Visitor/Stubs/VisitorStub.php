<?php

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Stubs;

use Netgen\BlockManager\Transfer\Output\VisitorInterface;

final class VisitorStub implements VisitorInterface
{
    public function accept($value)
    {
        return true;
    }

    public function visit($value, VisitorInterface $subVisitor = null)
    {
        return 'visited_value';
    }
}
