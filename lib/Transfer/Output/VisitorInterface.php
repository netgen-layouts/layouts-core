<?php

namespace Netgen\BlockManager\Transfer\Output;

/**
 * Visits values into hash representation.
 *
 * Hash format is either a scalar value, a hash array (associative array),
 * a pure numeric array or a nested combination of these.
 *
 * @see \Netgen\BlockManager\Transfer\SerializerInterface
 */
interface VisitorInterface
{
    /**
     * Check if the visitor accepts the given $value.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function accept($value);

    /**
     * Visit the given $value into hash representation.
     *
     * @param mixed $value
     * @param \Netgen\BlockManager\Transfer\Output\VisitorInterface|null $subVisitor
     *
     * @return mixed
     */
    public function visit($value, self $subVisitor = null);
}
