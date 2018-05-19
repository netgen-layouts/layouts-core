<?php

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\Collection\QueryType\QueryType;
use PHPUnit\Framework\TestCase;

final class QueryCreateStructTest extends TestCase
{
    public function testSetProperties()
    {
        $queryCreateStruct = new QueryCreateStruct(
            [
                'queryType' => new QueryType(),
            ]
        );

        $this->assertEquals(new QueryType(), $queryCreateStruct->queryType);
    }
}
