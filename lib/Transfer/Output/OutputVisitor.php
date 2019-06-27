<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output;

use Netgen\Layouts\Exception\RuntimeException;

final class OutputVisitor
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
     * Visit the given $value into hash array representation.
     *
     * @throws \Netgen\Layouts\Exception\RuntimeException if no sub-visitor is available for provided value
     */
    public function visit(object $value): array
    {
        foreach ($this->subVisitors as $subVisitor) {
            if ($subVisitor->accept($value)) {
                return $subVisitor->visit($value, $this);
            }
        }

        throw new RuntimeException(
            sprintf("No visitor available for value of type '%s'", get_class($value))
        );
    }
}
