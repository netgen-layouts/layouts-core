<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Stubs\VisitorStub;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(OutputVisitor::class)]
final class OutputVisitorTest extends TestCase
{
    private OutputVisitor $visitor;

    protected function setUp(): void
    {
        $this->visitor = new OutputVisitor([new VisitorStub()]);
    }

    public function testVisit(): void
    {
        self::assertSame(['visited_key' => 'visited_value'], $this->visitor->visit(new stdClass()));
    }

    public function testVisitWithNoAcceptedVisitorThrowsRuntimeException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No visitor available for value of type \'Netgen\Layouts\API\Values\Block\Block\'');

        $this->visitor = new OutputVisitor([]);

        $this->visitor->visit(new Block());
    }

    public static function acceptDataProvider(): iterable
    {
        return [
            [new Zone(), true],
            [new Layout(), true],
            [new Block(), true],
        ];
    }
}
