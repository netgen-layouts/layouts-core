<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Collection;

use Netgen\Layouts\Exception\Collection\QueryTypeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(QueryTypeException::class)]
final class QueryTypeExceptionTest extends TestCase
{
    public function testNoQueryType(): void
    {
        $exception = QueryTypeException::noQueryType('type');

        self::assertSame(
            'Query type with "type" identifier does not exist.',
            $exception->getMessage(),
        );
    }

    public function testNoForm(): void
    {
        $exception = QueryTypeException::noForm('type', 'form');

        self::assertSame(
            'Form "form" does not exist in "type" query type.',
            $exception->getMessage(),
        );
    }
}
