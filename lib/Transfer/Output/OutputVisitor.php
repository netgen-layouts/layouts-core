<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output;

use Netgen\Layouts\Exception\RuntimeException;

use function get_debug_type;
use function sprintf;

final class OutputVisitor
{
    /**
     * @var array<\Netgen\Layouts\Transfer\Output\VisitorInterface<object>>
     */
    private array $subVisitors = [];

    /**
     * @param iterable<\Netgen\Layouts\Transfer\Output\VisitorInterface<object>> $subVisitors
     */
    public function __construct(iterable $subVisitors)
    {
        foreach ($subVisitors as $subVisitor) {
            if ($subVisitor instanceof VisitorInterface) {
                $this->subVisitors[] = $subVisitor;
            }
        }
    }

    /**
     * Visit the given $value into hash array representation.
     *
     * @throws \Netgen\Layouts\Exception\RuntimeException if no sub-visitor is available for provided value
     *
     * @return array<string, mixed>
     */
    public function visit(object $value): array
    {
        foreach ($this->subVisitors as $subVisitor) {
            if ($subVisitor->accept($value)) {
                return $subVisitor->visit($value, $this);
            }
        }

        throw new RuntimeException(
            sprintf("No visitor available for value of type '%s'", get_debug_type($value)),
        );
    }
}
