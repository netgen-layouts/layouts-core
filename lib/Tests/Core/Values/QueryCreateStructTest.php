<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\API\Values\QueryCreateStruct;
use PHPUnit\Framework\TestCase;

class QueryCreateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $queryCreateStruct = new QueryCreateStruct();

        $this->assertNull($queryCreateStruct->identifier);
        $this->assertNull($queryCreateStruct->type);
    }

    public function testSetProperties()
    {
        $queryCreateStruct = new QueryCreateStruct(
            array(
                'identifier' => 'my_query',
                'type' => 'query_type',
            )
        );

        $this->assertEquals('my_query', $queryCreateStruct->identifier);
        $this->assertEquals('query_type', $queryCreateStruct->type);
    }
}
