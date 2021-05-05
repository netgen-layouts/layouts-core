<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Collection;

use Netgen\Layouts\Exception\Collection\QueryTypeException;
use PHPUnit\Framework\TestCase;

final class QueryTypeExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Exception\Collection\QueryTypeException::noQueryType
     */
    public function testNoQueryType(): void
    {
        $exception = QueryTypeException::noQueryType('type');

        self::assertSame(
            'Query type with "type" identifier does not exist.',
            $exception->getMessage(),
        );
    }

    /**
     * @covers \Netgen\Layouts\Exception\Collection\QueryTypeException::noForm
     */
    public function testNoForm(): void
    {
        $exception = QueryTypeException::noForm('type', 'form');

        self::assertSame(
            'Form "form" does not exist in "type" query type.',
            $exception->getMessage(),
        );
    }
}
