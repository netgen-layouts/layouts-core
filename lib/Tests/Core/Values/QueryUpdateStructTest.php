<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\API\Values\QueryUpdateStruct;
use PHPUnit\Framework\TestCase;

class QueryUpdateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $queryUpdateStruct = new QueryUpdateStruct();

        $this->assertNull($queryUpdateStruct->identifier);
    }

    public function testSetProperties()
    {
        $queryUpdateStruct = new QueryUpdateStruct(
            array(
                'identifier' => 'my_query',
            )
        );

        $this->assertEquals('my_query', $queryUpdateStruct->identifier);
    }
}
