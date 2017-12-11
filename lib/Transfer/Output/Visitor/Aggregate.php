<?php

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Netgen\BlockManager\Exception\InvalidInterfaceException;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Transfer\Output\Visitor;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

/**
 * Aggregate implementation of the Visitor.
 */
final class Aggregate extends Visitor
{
    /**
     * Internal collection of visitors.
     *
     * @var \Netgen\BlockManager\Transfer\Output\VisitorInterface[]
     */
    private $visitors = array();

    /**
     * Construct from the optional array of $visitors.
     *
     * @param \Netgen\BlockManager\Transfer\Output\VisitorInterface[] $visitors
     */
    public function __construct(array $visitors = array())
    {
        foreach ($visitors as $visitor) {
            if (!$visitor instanceof VisitorInterface) {
                throw new InvalidInterfaceException(
                    'Serialization visitor',
                    get_class($visitor),
                    VisitorInterface::class
                );
            }

            $this->visitors[] = $visitor;
        }
    }

    public function accept($value)
    {
        return true;
    }

    public function visit($value, VisitorInterface $subVisitor = null)
    {
        foreach ($this->visitors as $visitor) {
            if ($visitor->accept($value)) {
                return $visitor->visit($value, $this);
            }
        }

        $valueType = is_object($value) ? get_class($value) : var_export($value, true);

        throw new RuntimeException(
            sprintf("No visitor available for value of type '%s'", $valueType)
        );
    }
}
