<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Input\Result;

use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Transfer\Input\Result\SuccessResult;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class SuccessResultTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\Transfer\Input\Result\SuccessResult
     */
    private $result;

    /**
     * @var \Netgen\Layouts\Tests\API\Stubs\Value
     */
    private $entity;

    /**
     * @var \Ramsey\Uuid\UuidInterface
     */
    private $entityId;

    protected function setUp(): void
    {
        $this->entity = new Value();
        $this->entityId = Uuid::uuid4();

        $this->result = new SuccessResult('type', ['key' => 'value'], $this->entityId, $this->entity);
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Input\Result\SuccessResult::__construct
     * @covers \Netgen\Layouts\Transfer\Input\Result\SuccessResult::getEntityType
     */
    public function testGetEntityType(): void
    {
        self::assertSame('type', $this->result->getEntityType());
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Input\Result\SuccessResult::getData
     */
    public function testGetData(): void
    {
        self::assertSame(['key' => 'value'], $this->result->getData());
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Input\Result\SuccessResult::getEntityId
     */
    public function testGetEntityId(): void
    {
        self::assertSame($this->entityId, $this->result->getEntityId());
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Input\Result\SuccessResult::getEntity
     */
    public function testGetEntity(): void
    {
        self::assertSame($this->entity, $this->result->getEntity());
    }
}
