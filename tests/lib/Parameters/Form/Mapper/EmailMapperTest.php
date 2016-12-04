<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterMapper;

use Netgen\BlockManager\Parameters\Form\Mapper\EmailMapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class EmailMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\EmailMapper
     */
    protected $mapper;

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
