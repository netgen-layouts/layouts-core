<?php

namespace Netgen\BlockManager\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Form\DateTimeType;
use Netgen\BlockManager\Parameters\Form\Mapper\DateTimeMapper;
use PHPUnit\Framework\TestCase;

final class DateTimeMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\DateTimeMapper
     */
    private $mapper;

    public function setUp()
    {
        $this->mapper = new DateTimeMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\DateTimeMapper::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(DateTimeType::class, $this->mapper->getFormType());
    }
}
