<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\API\Values\QueryUpdateStruct;
use PHPUnit\Framework\TestCase;

class QueryUpdateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $queryUpdateStruct = new QueryUpdateStruct();

        self::assertNull($queryUpdateStruct->identifier);
    }

    public function testSetProperties()
    {
        $queryUpdateStruct = new QueryUpdateStruct(
            array(
                'identifier' => 'my_query',
            )
        );

        self::assertEquals('my_query', $queryUpdateStruct->identifier);
    }
}
