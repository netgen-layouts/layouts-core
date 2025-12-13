<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Input\Result;

use Exception;
use Netgen\Layouts\Transfer\Input\Result\ErrorResult;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(ErrorResult::class)]
final class ErrorResultTest extends TestCase
{
    private ErrorResult $result;

    private Uuid $entityId;

    private Exception $error;

    protected function setUp(): void
    {
        $this->entityId = Uuid::v4();
        $this->error = new Exception();

        $this->result = new ErrorResult('type', ['key' => 'data'], $this->entityId, $this->error);
    }

    public function testGetEntityType(): void
    {
        self::assertSame('type', $this->result->entityType);
    }

    public function testGetData(): void
    {
        self::assertSame(['key' => 'data'], $this->result->data);
    }

    public function testGetEntityId(): void
    {
        self::assertSame($this->entityId, $this->result->entityId);
    }

    public function testGetError(): void
    {
        self::assertSame($this->error, $this->result->error);
    }
}
