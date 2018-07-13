<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Stubs;

use Netgen\BlockManager\Transfer\Output\StatusStringTrait;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

final class ValueVisitor implements VisitorInterface
{
    use StatusStringTrait;

    public function accept($value): bool
    {
        return true;
    }

    public function visit($value, ?VisitorInterface $subVisitor = null)
    {
        return [
            'status' => $this->getStatusString($value),
        ];
    }
}
