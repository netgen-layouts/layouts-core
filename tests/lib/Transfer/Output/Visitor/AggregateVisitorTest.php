<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Stubs\VisitorStub;
use Netgen\Layouts\Transfer\Output\Visitor\AggregateVisitor;
use PHPUnit\Framework\TestCase;
use stdClass;

final class AggregateVisitorTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\Transfer\Output\Visitor\AggregateVisitor
     */
    private $visitor;

    protected function setUp(): void
    {
        $this->visitor = new AggregateVisitor([new VisitorStub()]);
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Output\Visitor\AggregateVisitor::visit
     */
    public function testVisit(): void
    {
        self::assertSame(['visited_value'], $this->visitor->visit(new stdClass()));
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Output\Visitor\AggregateVisitor::visit
     */
    public function testVisitWithNoAcceptedVisitorThrowsRuntimeException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No visitor available for value of type \'Netgen\\Layouts\\API\\Values\\Block\\Block\'');

        $this->visitor = new AggregateVisitor([]);

        $this->visitor->visit(new Block());
    }

    public function acceptProvider(): array
    {
        return [
            [new Zone(), true],
            [new Layout(), true],
            [new Block(), true],
        ];
    }
}
