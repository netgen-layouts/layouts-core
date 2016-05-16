<?php

namespace Netgen\BlockManager\Tests\Collection;

use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Configuration\QueryType\QueryType as Configuration;

class QueryTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Collection\QueryType
     */
    protected $queryType;

    public function setUp()
    {
        $this->queryType = new QueryType();
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType::setConfiguration
     * @covers \Netgen\BlockManager\Collection\QueryType::getConfiguration
     */
    public function testGetConfiguration()
    {
        $this->queryType->setConfiguration(new Configuration('identifier', array()));
        self::assertEquals(new Configuration('identifier', array()), $this->queryType->getConfiguration());
    }
}
