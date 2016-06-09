<?php

namespace Netgen\BlockManager\Tests\Configuration\Source;

use Netgen\BlockManager\Configuration\Source\Query;

class QueryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Netgen\BlockManager\Configuration\Source\Query
     */
    protected $query;

    public function setUp()
    {
        $this->query = new Query('default', 'ezcontent_search', array('parent_location_id' => 2));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Source\Query::__construct
     * @covers \Netgen\BlockManager\Configuration\Source\Query::getIdentifier
     */
    public function testGetIdentifier()
    {
        self::assertEquals('default', $this->query->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Source\Query::getQueryType
     */
    public function testGetQueryType()
    {
        self::assertEquals('ezcontent_search', $this->query->getQueryType());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Source\Query::getDefaultParameters
     */
    public function testGetDefaultParameters()
    {
        self::assertEquals(array('parent_location_id' => 2), $this->query->getDefaultParameters());
    }
}
