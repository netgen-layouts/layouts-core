<?php

namespace Netgen\BlockManager\Tests\Collection;

use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Configuration\QueryType\QueryType as Config;

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
     * @covers \Netgen\BlockManager\Collection\QueryType::setConfig
     * @covers \Netgen\BlockManager\Collection\QueryType::getConfig
     */
    public function testGetConfig()
    {
        $this->queryType->setConfig(new Config('identifier', array()));
        self::assertEquals(new Config('identifier', array()), $this->queryType->getConfig());
    }
}
