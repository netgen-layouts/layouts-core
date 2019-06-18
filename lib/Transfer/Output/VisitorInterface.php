<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output;

use Netgen\Layouts\Transfer\Output\Visitor\AggregateVisitor;

/**
 * Visits values into hash representation.
 *
 * Hash format is either a scalar value, a hash array (associative array),
 * a pure numeric array or a nested combination of these.
 *
 * @see \Netgen\Layouts\Transfer\SerializerInterface
 */
interface VisitorInterface
{
    /**
     * Check if the visitor accepts the given $value.
     */
    public function accept(object $value): bool;

    /**
     * Visit the given $value into hash array representation.
     */
    public function visit(object $value, AggregateVisitor $aggregateVisitor): array;
}
