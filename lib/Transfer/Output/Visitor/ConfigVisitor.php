<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Netgen\Layouts\API\Values\Config\Config;
use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

/**
 * Config value visitor.
 *
 * @see \Netgen\Layouts\API\Values\Config\Config
 */
final class ConfigVisitor implements VisitorInterface
{
    public function accept($value): bool
    {
        return $value instanceof Config;
    }

    /**
     * @param \Netgen\Layouts\API\Values\Config\Config $value
     * @param \Netgen\Layouts\Transfer\Output\Visitor\AggregateVisitor $aggregateVisitor
     *
     * @return mixed
     */
    public function visit($value, AggregateVisitor $aggregateVisitor)
    {
        return array_map(
            static function (Parameter $parameter) use ($aggregateVisitor) {
                return $aggregateVisitor->visit($parameter);
            },
            $value->getParameters()
        );
    }
}
