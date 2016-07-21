<?php

namespace Netgen\BlockManager\Tests\Configuration\Source;

use Netgen\BlockManager\Configuration\Source\Query;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
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
        $this->assertEquals('default', $this->query->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Source\Query::getQueryType
     */
    public function testGetQueryType()
    {
        $this->assertEquals('ezcontent_search', $this->query->getQueryType());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Source\Query::getDefaultParameters
     */
    public function testGetDefaultParameters()
    {
        $this->assertEquals(array('parent_location_id' => 2), $this->query->getDefaultParameters());
    }
}
