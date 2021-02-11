<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Mapper;

use Netgen\Layouts\Parameters\Form\Mapper\BooleanMapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

final class BooleanMapperTest extends TestCase
{
    private BooleanMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new BooleanMapper();
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Mapper\BooleanMapper::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(CheckboxType::class, $this->mapper->getFormType());
    }
}
