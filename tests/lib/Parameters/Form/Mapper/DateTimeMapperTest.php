<?php

namespace Netgen\BlockManager\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper\DateTimeMapper;
use Netgen\BlockManager\Parameters\Form\Type\DateTimeType;
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
