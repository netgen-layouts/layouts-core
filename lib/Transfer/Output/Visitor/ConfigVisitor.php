<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Netgen\Layouts\API\Values\Config\Config;
use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

use function array_map;

/**
 * Config value visitor.
 *
 * @see \Netgen\Layouts\API\Values\Config\Config
 *
 * @implements \Netgen\Layouts\Transfer\Output\VisitorInterface<\Netgen\Layouts\API\Values\Config\Config>
 */
final class ConfigVisitor implements VisitorInterface
{
    public function accept(object $value): bool
    {
        return $value instanceof Config;
    }

    public function visit(object $value, OutputVisitor $outputVisitor): array
    {
        return array_map(
            static function (Parameter $parameter) {
                $definition = $parameter->getParameterDefinition();

                return $definition->getType()->export($definition, $parameter->getValue());
            },
            $value->getParameters(),
        );
    }
}
