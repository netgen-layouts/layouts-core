<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\Tests\Transfer\Output\Visitor\Stubs\VisitorStub;
use Netgen\BlockManager\Transfer\Output\Visitor\AggregateVisitor;
use PHPUnit\Framework\TestCase;

final class AggregateVisitorTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Transfer\Output\Visitor\AggregateVisitor
     */
    private $visitor;

    public function setUp(): void
    {
        $this->visitor = new AggregateVisitor([new VisitorStub()]);
    }

    /**
     * @param mixed $value
     * @param bool $accepted
     *
     * @covers \Netgen\BlockManager\Transfer\Output\Visitor\AggregateVisitor::__construct
     * @covers \Netgen\BlockManager\Transfer\Output\Visitor\AggregateVisitor::accept
     *
     * @dataProvider acceptProvider
     */
    public function testAccept($value, bool $accepted): void
    {
        self::assertSame($accepted, $this->visitor->accept($value));
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Output\Visitor\AggregateVisitor::visit
     */
    public function testVisit(): void
    {
        self::assertSame('visited_value', $this->visitor->visit(42));
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Output\Visitor\AggregateVisitor::visit
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage No visitor available for value of type 'Netgen\BlockManager\API\Values\Block\Block'
     */
    public function testVisitWithNoAcceptedVisitorAndObjectValueThrowsRuntimeException(): void
    {
        $this->visitor = new AggregateVisitor([]);

        $this->visitor->visit(new Block());
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Output\Visitor\AggregateVisitor::visit
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage No visitor available for value of type 'integer'
     */
    public function testVisitWithNoAcceptedVisitorAndScalarValueThrowsRuntimeException(): void
    {
        $this->visitor = new AggregateVisitor([]);

        $this->visitor->visit(42);
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
