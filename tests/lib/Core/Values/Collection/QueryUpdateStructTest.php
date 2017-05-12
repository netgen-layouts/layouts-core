<?php

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\TestCase;

class QueryUpdateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $queryUpdateStruct = new QueryUpdateStruct();

        $this->assertNull($queryUpdateStruct->queryType);
    }

    public function testSetProperties()
    {
        $queryUpdateStruct = new QueryUpdateStruct(
            array(
                'queryType' => new QueryType('type'),
            )
        );

        $this->assertEquals(new QueryType('type'), $queryUpdateStruct->queryType);
    }
}
