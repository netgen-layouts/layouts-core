<?php

namespace Netgen\BlockManager\Transfer\Serializer\Visitor;

use Netgen\BlockManager\Transfer\Serializer\Visitor;
use RuntimeException;

/**
 * Aggregate implementation of the Visitor.
 */
class Aggregate extends Visitor
{
    /**
     * Internal collection of visitors.
     *
     * @var \Netgen\BlockManager\Transfer\Serializer\Visitor[]
     */
    private $visitors = array();

    /**
     * Construct from the optional array of $visitors.
     *
     * @param \Netgen\BlockManager\Transfer\Serializer\Visitor[] $visitors
     */
    public function __construct(array $visitors = array())
    {
        foreach ($visitors as $visitor) {
            $this->addVisitor($visitor);
        }
    }

    /**
     * Add given $visitor to the internal collection.
     *
     * @param \Netgen\BlockManager\Transfer\Serializer\Visitor $visitor
     */
    public function addVisitor(Visitor $visitor)
    {
        $this->visitors[] = $visitor;
    }

    public function accept($value)
    {
        return true;
    }

    public function visit($value, Visitor $subVisitor = null)
    {
        foreach ($this->visitors as $visitor) {
            if ($visitor->accept($value)) {
                return $visitor->visit($value, $this);
            }
        }

        $valueType = is_object($value) ? get_class($value) : var_export($value, true);

        throw new RuntimeException(
            "No visitor available for value of type '{$valueType}'"
        );
    }
}
