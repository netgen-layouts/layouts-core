<?php

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Netgen\BlockManager\Exception\InvalidInterfaceException;
use Netgen\BlockManager\Transfer\Output\Visitor;
use RuntimeException;

/**
 * Aggregate implementation of the Visitor.
 */
final class Aggregate extends Visitor
{
    /**
     * Internal collection of visitors.
     *
     * @var \Netgen\BlockManager\Transfer\Output\Visitor[]
     */
    private $visitors = array();

    /**
     * Construct from the optional array of $visitors.
     *
     * @param \Netgen\BlockManager\Transfer\Output\Visitor[] $visitors
     */
    public function __construct(array $visitors = array())
    {
        foreach ($visitors as $visitor) {
            if (!$visitor instanceof Visitor) {
                throw new InvalidInterfaceException(
                    'Serialization visitor',
                    get_class($visitor),
                    Visitor::class
                );
            }

            $this->visitors[] = $visitor;
        }
    }

    public function accept($value)
    {
        return true;
    }

    public function visit($value, Visitor $subVisitor = null, array $context = null)
    {
        foreach ($this->visitors as $visitor) {
            if ($visitor->accept($value)) {
                return $visitor->visit($value, $this, $context);
            }
        }

        $valueType = is_object($value) ? get_class($value) : var_export($value, true);

        throw new RuntimeException(
            "No visitor available for value of type '{$valueType}'"
        );
    }
}
