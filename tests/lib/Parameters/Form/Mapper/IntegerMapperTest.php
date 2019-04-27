<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Mapper;

use Netgen\Layouts\Parameters\Form\Mapper\IntegerMapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

final class IntegerMapperTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\Parameters\Form\Mapper\IntegerMapper
     */
    private $mapper;

    protected function setUp(): void
    {
        $this->mapper = new IntegerMapper();
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Mapper\IntegerMapper::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(IntegerType::class, $this->mapper->getFormType());
    }
}
