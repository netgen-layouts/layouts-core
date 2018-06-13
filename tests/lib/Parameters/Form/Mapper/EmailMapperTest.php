<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper\EmailMapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

final class EmailMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\EmailMapper
     */
    private $mapper;

    public function setUp()
    {
        $this->mapper = new EmailMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\EmailMapper::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(EmailType::class, $this->mapper->getFormType());
    }
}
