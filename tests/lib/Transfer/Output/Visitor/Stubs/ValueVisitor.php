<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Stubs;

use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\StatusStringTrait;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

final class ValueVisitor implements VisitorInterface
{
    use StatusStringTrait;

    public function accept(object $value): bool
    {
        return $value instanceof Value;
    }

    /**
     * @param \Netgen\Layouts\API\Values\Value $value
     * @param \Netgen\Layouts\Transfer\Output\OutputVisitor $outputVisitor
     *
     * @return array
     */
    public function visit(object $value, OutputVisitor $outputVisitor): array
    {
        return [
            'status' => $this->getStatusString($value),
        ];
    }
}
