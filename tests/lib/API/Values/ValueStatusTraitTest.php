<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values;

use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\Tests\API\Stubs\Value;
use PHPUnit\Framework\TestCase;

final class ValueStatusTraitTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\API\Values\ValueStatusTrait::getStatus
     * @covers \Netgen\Layouts\API\Values\ValueStatusTrait::isArchived
     * @covers \Netgen\Layouts\API\Values\ValueStatusTrait::isDraft
     * @covers \Netgen\Layouts\API\Values\ValueStatusTrait::isPublished
     *
     * @dataProvider statusDataProvider
     */
    public function testStatus(Status $status, bool $isDraft, bool $isPublished, bool $isArchived): void
    {
        $value = Value::fromArray(['status' => $status]);

        self::assertSame($status, $value->getStatus());
        self::assertSame($isDraft, $value->isDraft());
        self::assertSame($isPublished, $value->isPublished());
        self::assertSame($isArchived, $value->isArchived());
    }

    public static function statusDataProvider(): iterable
    {
        return [
            [Status::Draft, true, false, false],
            [Status::Published, false, true, false],
            [Status::Archived, false, false, true],
        ];
    }
}
