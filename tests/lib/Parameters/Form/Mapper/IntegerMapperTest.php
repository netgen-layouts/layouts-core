<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper\IntegerMapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

final class IntegerMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\IntegerMapper
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new IntegerMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\IntegerMapper::getFormType
     */
    public function testGetFormType(): void
    {
        $this->assertEquals(IntegerType::class, $this->mapper->getFormType());
    }
}
