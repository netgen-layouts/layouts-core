<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\API\Values\QueryCreateStruct;
use PHPUnit\Framework\TestCase;

class QueryCreateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $queryCreateStruct = new QueryCreateStruct();

        self::assertNull($queryCreateStruct->identifier);
        self::assertNull($queryCreateStruct->type);
    }

    public function testSetProperties()
    {
        $queryCreateStruct = new QueryCreateStruct(
            array(
                'identifier' => 'my_query',
                'type' => 'ezcontent_search',
            )
        );

        self::assertEquals('my_query', $queryCreateStruct->identifier);
        self::assertEquals('ezcontent_search', $queryCreateStruct->type);
    }
}
