<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Input\Result;

use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Transfer\Input\Result\SuccessResult;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[CoversClass(SuccessResult::class)]
final class SuccessResultTest extends TestCase
{
    private SuccessResult $result;

    private Value $entity;

    private UuidInterface $entityId;

    protected function setUp(): void
    {
        $this->entity = new Value();
        $this->entityId = Uuid::uuid4();

        $this->result = new SuccessResult('type', ['key' => 'value'], $this->entityId, $this->entity);
    }

    public function testGetEntityType(): void
    {
        self::assertSame('type', $this->result->getEntityType());
    }

    public function testGetData(): void
    {
        self::assertSame(['key' => 'value'], $this->result->getData());
    }

    public function testGetEntityId(): void
    {
        self::assertSame($this->entityId, $this->result->getEntityId());
    }

    public function testGetEntity(): void
    {
        self::assertSame($this->entity, $this->result->getEntity());
    }
}
