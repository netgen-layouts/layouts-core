<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output;

use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Stubs\ValueVisitor;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\StatusStringTrait;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversTrait(StatusStringTrait::class)]
final class StatusStringTraitTest extends TestCase
{
    private ValueVisitor $visitor;

    protected function setUp(): void
    {
        $this->visitor = new ValueVisitor();
    }

    /**
     * @param mixed[] $visitedValue
     */
    #[DataProvider('visitDataProvider')]
    public function testVisit(Status $status, array $visitedValue): void
    {
        self::assertSame($visitedValue, $this->visitor->visit(Value::fromArray(['status' => $status]), new OutputVisitor([])));
    }

    public static function visitDataProvider(): iterable
    {
        return [
            [Status::Draft, ['status' => 'draft']],
            [Status::Published, ['status' => 'published']],
            [Status::Archived, ['status' => 'archived']],
        ];
    }
}
