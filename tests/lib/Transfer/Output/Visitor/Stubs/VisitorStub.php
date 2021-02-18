<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Stubs;

use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

/**
 * @implements \Netgen\Layouts\Transfer\Output\VisitorInterface<object>
 */
final class VisitorStub implements VisitorInterface
{
    public function accept(object $value): bool
    {
        return true;
    }

    public function visit(object $value, OutputVisitor $outputVisitor): array
    {
        return ['visited_key' => 'visited_value'];
    }
}
