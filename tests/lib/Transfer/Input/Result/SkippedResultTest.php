<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Input\Result;

use Netgen\Layouts\Transfer\Input\Result\SkippedResult;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class SkippedResultTest extends TestCase
{
    private SkippedResult $result;

    private UuidInterface $entityId;

    protected function setUp(): void
    {
        $this->entityId = Uuid::uuid4();

        $this->result = new SkippedResult('type', ['key' => 'value'], $this->entityId);
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Input\Result\SkippedResult::__construct
     * @covers \Netgen\Layouts\Transfer\Input\Result\SkippedResult::getEntityType
     */
    public function testGetEntityType(): void
    {
        self::assertSame('type', $this->result->getEntityType());
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Input\Result\SkippedResult::getData
     */
    public function testGetData(): void
    {
        self::assertSame(['key' => 'value'], $this->result->getData());
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Input\Result\SkippedResult::getEntityId
     */
    public function testGetEntityId(): void
    {
        self::assertSame($this->entityId, $this->result->getEntityId());
    }
}
