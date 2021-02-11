<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Mapper;

use Netgen\Layouts\Parameters\Form\Mapper\EmailMapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

final class EmailMapperTest extends TestCase
{
    private EmailMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new EmailMapper();
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Mapper\EmailMapper::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(EmailType::class, $this->mapper->getFormType());
    }
}
