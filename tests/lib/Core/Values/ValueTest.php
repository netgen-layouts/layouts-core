<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;

final class ValueTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Value::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Value::isArchived
     * @covers \Netgen\BlockManager\Core\Values\Value::isDraft
     * @covers \Netgen\BlockManager\Core\Values\Value::isPublished
     *
     * @dataProvider statusProvider
     */
    public function testStatus(int $status, bool $isDraft, bool $isPublished, bool $isArchived): void
    {
        $value = new Value(['status' => $status]);

        $this->assertEquals($status, $value->getStatus());
        $this->assertEquals($isDraft, $value->isDraft());
        $this->assertEquals($isPublished, $value->isPublished());
        $this->assertEquals($isArchived, $value->isArchived());
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
