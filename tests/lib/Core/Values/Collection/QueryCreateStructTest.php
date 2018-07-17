<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\Collection\QueryType\QueryType;
use PHPUnit\Framework\TestCase;

final class QueryCreateStructTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\API\Values\Collection\QueryCreateStruct::__construct
     * @covers \Netgen\BlockManager\API\Values\Collection\QueryCreateStruct::getQueryType
     */
    public function testConstructor(): void
    {
        $queryType = new QueryType();

        $queryCreateStruct = new QueryCreateStruct($queryType);

        $this->assertSame($queryType, $queryCreateStruct->getQueryType());
    }
}
