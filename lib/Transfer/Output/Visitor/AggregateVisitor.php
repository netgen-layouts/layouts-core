<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

final class AggregateVisitor
{
    /**
     * @var \Netgen\Layouts\Transfer\Output\VisitorInterface[]
     */
    private $subVisitors = [];

    public function __construct(iterable $subVisitors)
    {
        foreach ($subVisitors as $key => $subVisitor) {
            if ($subVisitor instanceof VisitorInterface) {
                $this->subVisitors[$key] = $subVisitor;
            }
        }
    }

    /**
     * Visit the given $value into hash representation.
     *
     * @param mixed $value
     *
     * @throws \Netgen\Layouts\Exception\RuntimeException if no sub-visitor is available for provided value
     *
     * @return array
     */
    public function visit($value): array
    {
        foreach ($this->subVisitors as $subVisitor) {
            if ($subVisitor->accept($value)) {
                return $subVisitor->visit($value, $this);
            }
        }

        $valueType = is_object($value) ? get_class($value) : gettype($value);

        throw new RuntimeException(
            sprintf("No visitor available for value of type '%s'", $valueType)
        );
    }
}
