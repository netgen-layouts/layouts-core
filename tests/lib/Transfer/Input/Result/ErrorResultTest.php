<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Input\Result;

use Exception;
use Netgen\Layouts\Transfer\Input\Result\ErrorResult;
use PHPUnit\Framework\TestCase;

final class ErrorResultTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\Transfer\Input\Result\ErrorResult
     */
    private $result;

    /**
     * @var \Throwable
     */
    private $error;

    public function setUp(): void
    {
        $this->error = new Exception();

        $this->result = new ErrorResult('type', ['data'], $this->error);
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Input\Result\ErrorResult::__construct
     * @covers \Netgen\Layouts\Transfer\Input\Result\ErrorResult::getEntityType
     */
    public function testGetEntityType(): void
    {
        self::assertSame('type', $this->result->getEntityType());
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Input\Result\ErrorResult::getData
     */
    public function testGetData(): void
    {
        self::assertSame(['data'], $this->result->getData());
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Input\Result\ErrorResult::getError
     */
    public function testGetError(): void
    {
        self::assertSame($this->error, $this->result->getError());
    }
}
