<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;

final class ValueStatusTraitTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\ValueStatusTrait::getStatus
     * @covers \Netgen\BlockManager\Core\Values\ValueStatusTrait::isArchived
     * @covers \Netgen\BlockManager\Core\Values\ValueStatusTrait::isDraft
     * @covers \Netgen\BlockManager\Core\Values\ValueStatusTrait::isPublished
     *
     * @dataProvider statusProvider
     */
    public function testStatus(int $status, bool $isDraft, bool $isPublished, bool $isArchived): void
    {
        $value = new Value(['status' => $status]);

        $this->assertSame($status, $value->getStatus());
        $this->assertSame($isDraft, $value->isDraft());
        $this->assertSame($isPublished, $value->isPublished());
        $this->assertSame($isArchived, $value->isArchived());
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
