<?php

namespace Netgen\BlockManager\Tests\Transfer\Output;

use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\Transfer\Output\Visitor\Stubs\ValueVisitor;
use PHPUnit\Framework\TestCase;

final class VisitorTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Tests\Transfer\Output\Visitor\Stubs\ValueVisitor
     */
    private $visitor;

    public function setUp()
    {
        $this->visitor = new ValueVisitor();
    }

    /**
     * @param int $status
     * @param array $visitedValue
     *
     * @covers \Netgen\BlockManager\Transfer\Output\Visitor::getStatusString
     *
     * @dataProvider visitProvider
     */
    public function testVisit($status, array $visitedValue)
    {
        $this->assertEquals($visitedValue, $this->visitor->visit(new Value(['status' => $status])));
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Output\Visitor::getStatusString
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Unknown status '9999'
     */
    public function testVisitThrowsRuntimeExceptionWithInvalidStatus()
    {
        $this->visitor->visit(new Value(['status' => 9999]));
    }

    public function visitProvider()
    {
        return [
            [Value::STATUS_DRAFT, ['status' => 'DRAFT']],
            [Value::STATUS_PUBLISHED, ['status' => 'PUBLISHED']],
            [Value::STATUS_ARCHIVED, ['status' => 'ARCHIVED']],
        ];
    }
}
