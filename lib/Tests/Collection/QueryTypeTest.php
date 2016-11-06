<?php

namespace Netgen\BlockManager\Tests\Collection;

use Netgen\BlockManager\Collection\QueryType\Configuration\Configuration;
use Netgen\BlockManager\Collection\QueryType;
use PHPUnit\Framework\TestCase;

class QueryTypeTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Netgen\BlockManager\Collection\QueryType
     */
    protected $queryType;

    public function setUp()
    {
        $this->configMock = $this->createMock(Configuration::class);

        $this->queryType = new QueryType(
            array(
                'type' => 'query_type',
                'config' => $this->configMock,
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType::__construct
     * @covers \Netgen\BlockManager\Collection\QueryType::getType
     */
    public function testGetType()
    {
        $this->assertEquals('query_type', $this->queryType->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType::getConfig
     */
    public function testGetConfig()
    {
        $this->assertEquals($this->configMock, $this->queryType->getConfig());
    }
}
