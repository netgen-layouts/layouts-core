<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Netgen\Layouts\API\Values\Config\Config;
use Netgen\Layouts\Exception\RuntimeException;
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
     * @param \Netgen\Layouts\Transfer\Output\VisitorInterface|null $subVisitor
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
