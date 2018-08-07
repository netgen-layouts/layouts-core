<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Input\Result;

use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Transfer\Input\Result\SuccessResult;
use PHPUnit\Framework\TestCase;

final class SuccessResultTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Transfer\Input\Result\SuccessResult
     */
    private $result;

    /**
     * @var \Netgen\BlockManager\Tests\Core\Stubs\Value
     */
    private $entity;

    public function setUp(): void
    {
        $this->entity = new Value();

        $this->result = new SuccessResult('type', ['data'], 42, $this->entity);
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Input\Result\SuccessResult::__construct
     * @covers \Netgen\BlockManager\Transfer\Input\Result\SuccessResult::getEntityType
     */
    public function testGetEntityType(): void
    {
        self::assertSame('type', $this->result->getEntityType());
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Input\Result\SuccessResult::getData
     */
    public function testGetData(): void
    {
        self::assertSame(['data'], $this->result->getData());
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Input\Result\SuccessResult::getEntityId
     */
    public function testGetEntityId(): void
    {
        self::assertSame(42, $this->result->getEntityId());
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Input\Result\SuccessResult::getEntity
     */
    public function testGetEntity(): void
    {
        self::assertSame($this->entity, $this->result->getEntity());
    }
}
