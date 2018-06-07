<?php

namespace Netgen\BlockManager\Tests\Transfer\Input\Result;

use Exception;
use Netgen\BlockManager\Transfer\Input\Result\ErrorResult;
use PHPUnit\Framework\TestCase;

final class ErrorResultTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Transfer\Input\Result\ErrorResult
     */
    private $result;

    public function setUp()
    {
        $this->result = new ErrorResult('type', ['data'], new Exception());
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Input\Result\ErrorResult::__construct
     * @covers \Netgen\BlockManager\Transfer\Input\Result\ErrorResult::getEntityType
     */
    public function testGetEntityType()
    {
        $this->assertEquals('type', $this->result->getEntityType());
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Input\Result\ErrorResult::getData
     */
    public function testGetData()
    {
        $this->assertEquals(['data'], $this->result->getData());
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Input\Result\ErrorResult::getError
     */
    public function testGetError()
    {
        $this->assertEquals(new Exception(), $this->result->getError());
    }
}
