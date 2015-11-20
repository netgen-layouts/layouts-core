<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Exception;

use Netgen\Bundle\BlockManagerBundle\Exception\InternalServerErrorHttpException;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit_Framework_TestCase;

class InternalServerErrorHttpExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Exception\InternalServerErrorHttpException::__construct
     */
    public function testExceptionMessage()
    {
        $exception = new InternalServerErrorHttpException('An error occurred. That is all we know.');

        self::assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getStatusCode());
        self::assertEquals('An error occurred. That is all we know.', $exception->getMessage());
    }
}
