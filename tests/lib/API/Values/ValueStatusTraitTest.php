<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\API\Values;

use Netgen\BlockManager\Tests\API\Stubs\Value;
use PHPUnit\Framework\TestCase;

final class ValueStatusTraitTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\API\Values\ValueStatusTrait::getStatus
     * @covers \Netgen\BlockManager\API\Values\ValueStatusTrait::isArchived
     * @covers \Netgen\BlockManager\API\Values\ValueStatusTrait::isDraft
     * @covers \Netgen\BlockManager\API\Values\ValueStatusTrait::isPublished
     *
     * @dataProvider statusProvider
     */
    public function testStatus(int $status, bool $isDraft, bool $isPublished, bool $isArchived): void
    {
        $value = Value::fromArray(['status' => $status]);

        self::assertSame($status, $value->getStatus());
        self::assertSame($isDraft, $value->isDraft());
        self::assertSame($isPublished, $value->isPublished());
        self::assertSame($isArchived, $value->isArchived());
    }

    public function statusProvider(): array
    {
        return [
            [Value::STATUS_DRAFT, true, false, false],
            [Value::STATUS_PUBLISHED, false, true, false],
            [Value::STATUS_ARCHIVED, false, false, true],
        ];
    }
}
