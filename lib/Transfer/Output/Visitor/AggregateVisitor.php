<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

/**
 * Aggregate implementation of the Visitor.
 */
final class AggregateVisitor implements VisitorInterface
{
    /**
     * Internal collection of visitors.
     *
     * @var \Netgen\BlockManager\Transfer\Output\VisitorInterface[]
     */
    private $visitors = [];

    public function __construct(iterable $visitors)
    {
        foreach ($visitors as $key => $visitor) {
            if ($visitor instanceof VisitorInterface) {
                $this->visitors[$key] = $visitor;
            }
        }
    }

    public function accept($value): bool
    {
        return true;
    }

    public function visit($value, ?VisitorInterface $subVisitor = null)
    {
        foreach ($this->visitors as $visitor) {
            if ($visitor->accept($value)) {
                return $visitor->visit($value, $this);
            }
        }

        $valueType = is_object($value) ? get_class($value) : gettype($value);

        throw new RuntimeException(
            sprintf("No visitor available for value of type '%s'", $valueType)
        );
    }
}
