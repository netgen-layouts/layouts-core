<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values;

use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Tests\API\Stubs\Value;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversTrait(ValueStatusTrait::class)]
final class ValueStatusTraitTest extends TestCase
{
    #[DataProvider('statusDataProvider')]
    public function testStatus(Status $status, bool $isDraft, bool $isPublished, bool $isArchived): void
    {
        $value = Value::fromArray(['status' => $status]);

        self::assertSame($status, $value->status);
        self::assertSame($isDraft, $value->isDraft);
        self::assertSame($isPublished, $value->isPublished);
        self::assertSame($isArchived, $value->isArchived);
    }

    /**
     * @return iterable<mixed>
     */
    public static function statusDataProvider(): iterable
    {
        return [
            [Status::Draft, true, false, false],
            [Status::Published, false, true, false],
            [Status::Archived, false, false, true],
        ];
    }
}
