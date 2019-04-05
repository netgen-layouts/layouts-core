<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Netgen\BlockManager\API\Values\Config\Config;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

/**
 * Config value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\Config\Config
 */
final class ConfigVisitor implements VisitorInterface
{
    public function accept($value): bool
    {
        return $value instanceof Config;
    }

    /**
     * @param \Netgen\BlockManager\API\Values\Config\Config $value
     * @param \Netgen\BlockManager\Transfer\Output\VisitorInterface|null $subVisitor
     *
     * @return mixed
     */
    public function visit($value, ?VisitorInterface $subVisitor = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

        return array_map(
            static function (Parameter $parameter) use ($subVisitor) {
                return $subVisitor->visit($parameter);
            },
            $value->getParameters()
        );
    }
}
