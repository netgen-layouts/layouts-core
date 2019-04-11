<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

/**
 * Aggregate implementation of the Visitor.
 */
final class AggregateVisitor implements VisitorInterface
{
    /**
     * Internal collection of visitors.
     *
     * @var \Netgen\Layouts\Transfer\Output\VisitorInterface[]
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
