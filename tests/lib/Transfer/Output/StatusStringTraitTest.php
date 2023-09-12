<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output;

use Netgen\Layouts\Exception\RuntimeException;
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
    public function testVisit(int $status, array $visitedValue): void
    {
        self::assertSame($visitedValue, $this->visitor->visit(Value::fromArray(['status' => $status]), new OutputVisitor([])));
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Output\StatusStringTrait::getStatusString
     */
    public function testVisitThrowsRuntimeExceptionWithInvalidStatus(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unknown status \'9999\'');

        $this->visitor->visit(Value::fromArray(['status' => 9999]), new OutputVisitor([]));
    }

    public static function visitDataProvider(): iterable
    {
        return [
            [Value::STATUS_DRAFT, ['status' => 'DRAFT']],
            [Value::STATUS_PUBLISHED, ['status' => 'PUBLISHED']],
            [Value::STATUS_ARCHIVED, ['status' => 'ARCHIVED']],
        ];
    }
}
