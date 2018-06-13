<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper\BooleanMapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

final class BooleanMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\BooleanMapper
     */
    private $mapper;

    public function setUp()
    {
        $this->mapper = new BooleanMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\BooleanMapper::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(CheckboxType::class, $this->mapper->getFormType());
    }
}
