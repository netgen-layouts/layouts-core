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

    public function visit($config, ?VisitorInterface $subVisitor = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

        /* @var \Netgen\BlockManager\API\Values\Config\Config $config */

        return array_map(
            function (Parameter $parameter) use ($subVisitor) {
                return $subVisitor->visit($parameter);
            },
            $config->getParameters()
        );
    }
}
