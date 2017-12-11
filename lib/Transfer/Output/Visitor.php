<?php

namespace Netgen\BlockManager\Transfer\Output;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Exception\RuntimeException;

abstract class Visitor implements VisitorInterface
{
    /**
     * Return status string representation for the given $layout.
     *
     * @param \Netgen\BlockManager\API\Values\Value $value
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException If status is not recognized
     *
     * @return string
     */
    protected function getStatusString(Value $value)
    {
        switch ($value->getStatus()) {
            case Value::STATUS_DRAFT:
                return 'DRAFT';
            case Value::STATUS_PUBLISHED:
                return 'PUBLISHED';
            case Value::STATUS_ARCHIVED:
                return 'ARCHIVED';
        }

        $statusString = var_export($value->getStatus(), true);

        throw new RuntimeException(sprintf("Unknown status '%s'", $statusString));
    }
}
