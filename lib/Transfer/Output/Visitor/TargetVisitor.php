<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

/**
 * Target value visitor.
 *
 * @see \Netgen\Layouts\API\Values\LayoutResolver\Target
 */
final class TargetVisitor implements VisitorInterface
{
    public function accept(object $value): bool
    {
        return $value instanceof Target;
    }

    /**
     * @param \Netgen\Layouts\API\Values\LayoutResolver\Target $value
     * @param \Netgen\Layouts\Transfer\Output\OutputVisitor $outputVisitor
     *
     * @return array<string, mixed>
     */
    public function visit(object $value, OutputVisitor $outputVisitor): array
    {
        return [
            'id' => $value->getId()->toString(),
            'type' => $value->getTargetType()::getType(),
            'value' => $value->getValue(),
        ];
    }
}
