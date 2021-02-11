<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Mapper;

use Netgen\Layouts\Parameters\Form\Mapper\IdentifierMapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class IdentifierMapperTest extends TestCase
{
    private IdentifierMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new IdentifierMapper();
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Mapper\IdentifierMapper::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(TextType::class, $this->mapper->getFormType());
    }
}
