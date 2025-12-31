<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Input\Result;

use Netgen\Layouts\Transfer\Input\Result\SkippedResult;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(SkippedResult::class)]
final class SkippedResultTest extends TestCase
{
    private SkippedResult $result;

    private Uuid $entityId;

    protected function setUp(): void
    {
        $this->entityId = Uuid::v7();

        $this->result = new SkippedResult('type', ['key' => 'value'], $this->entityId);
    }

    public function testGetEntityType(): void
    {
        self::assertSame('type', $this->result->entityType);
    }

    public function testGetData(): void
    {
        self::assertSame(['key' => 'value'], $this->result->data);
    }

    public function testGetEntityId(): void
    {
        self::assertSame($this->entityId, $this->result->entityId);
    }
}
