<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Exception\Collection;

use Netgen\BlockManager\Exception\Collection\QueryTypeException;
use PHPUnit\Framework\TestCase;

final class QueryTypeExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Collection\QueryTypeException::noQueryType
     */
    public function testNoQueryType(): void
    {
        $exception = QueryTypeException::noQueryType('type');

        $this->assertSame(
            'Query type with "type" identifier does not exist.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Collection\QueryTypeException::noForm
     */
    public function testNoForm(): void
    {
        $exception = QueryTypeException::noForm('type', 'form');

        $this->assertSame(
            'Form "form" does not exist in "type" query type.',
            $exception->getMessage()
        );
    }
}
