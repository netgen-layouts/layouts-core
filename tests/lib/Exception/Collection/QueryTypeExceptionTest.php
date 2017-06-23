<?php

namespace Netgen\BlockManager\Tests\Exception\Collection;

use Netgen\BlockManager\Exception\Collection\QueryTypeException;
use PHPUnit\Framework\TestCase;

class QueryTypeExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Collection\QueryTypeException::noQueryType
     */
    public function testNoQueryType()
    {
        $exception = QueryTypeException::noQueryType('type');

        $this->assertEquals(
            'Query type with "type" identifier does not exist.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Collection\QueryTypeException::noForm
     */
    public function testNoForm()
    {
        $exception = QueryTypeException::noForm('type', 'form');

        $this->assertEquals(
            'Form "form" does not exist in "type" query type.',
            $exception->getMessage()
        );
    }
}
