<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output;

use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Stubs\ValueVisitor;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use PHPUnit\Framework\TestCase;

final class StatusStringTraitTest extends TestCase
{
    private ValueVisitor $visitor;

    protected function setUp(): void
    {
        $this->visitor = new ValueVisitor();
    }

    /**
     * @param mixed[] $visitedValue
     *
     * @covers \Netgen\Layouts\Transfer\Output\StatusStringTrait::getStatusString
     *
     * @dataProvider visitDataProvider
     */
    public function testVisit(Status $status, array $visitedValue): void
    {
        self::assertSame($visitedValue, $this->visitor->visit(Value::fromArray(['status' => $status]), new OutputVisitor([])));
    }

    public static function visitDataProvider(): iterable
    {
        return [
            [Status::Draft, ['status' => 'DRAFT']],
            [Status::Published, ['status' => 'PUBLISHED']],
            [Status::Archived, ['status' => 'ARCHIVED']],
        ];
    }
}
