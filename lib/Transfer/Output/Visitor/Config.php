<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Netgen\BlockManager\API\Values\Config\Config as ConfigValue;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Transfer\Output\Visitor;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

/**
 * Config value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\Config\Config
 */
final class Config extends Visitor
{
    public function accept($value)
    {
        return $value instanceof ConfigValue;
    }

    public function visit($config, VisitorInterface $subVisitor = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

        /** @var \Netgen\BlockManager\API\Values\Config\Config $config */
        $hash = [];

        foreach ($config->getParameters() as $parameter) {
            $hash[$parameter->getName()] = $subVisitor->visit($parameter);
        }

        return $hash;
    }
}
