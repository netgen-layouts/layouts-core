<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Stubs;

use Netgen\Layouts\Transfer\Output\StatusStringTrait;
use Netgen\Layouts\Transfer\Output\Visitor\AggregateVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

final class ValueVisitor implements VisitorInterface
{
    use StatusStringTrait;

    public function accept($value): bool
    {
        return true;
    }

    public function visit($value, AggregateVisitor $aggregateVisitor)
    {
        return [
            'status' => $this->getStatusString($value),
        ];
    }
}
