<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Exception;

use Netgen\Bundle\BlockManagerBundle\Exception\InternalServerErrorHttpException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class InternalServerErrorHttpExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Exception\InternalServerErrorHttpException::__construct
     */
    public function testExceptionMessage()
    {
        $exception = new InternalServerErrorHttpException('An error occurred. That is all we know.');

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getStatusCode());
        $this->assertEquals('An error occurred. That is all we know.', $exception->getMessage());
    }
}
