<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Stubs;

use Netgen\BlockManager\Transfer\Output\VisitorInterface;

final class VisitorStub implements VisitorInterface
{
    public function accept($value): bool
    {
        return true;
    }

    public function visit($value, ?VisitorInterface $subVisitor = null)
    {
        return 'visited_value';
    }
}
