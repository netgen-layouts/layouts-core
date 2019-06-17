<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Stubs;

use Netgen\Layouts\Transfer\Output\Visitor\AggregateVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

final class VisitorStub implements VisitorInterface
{
    public function accept($value): bool
    {
        return true;
    }

    public function visit($value, AggregateVisitor $aggregateVisitor)
    {
        return ['visited_value'];
    }
}
