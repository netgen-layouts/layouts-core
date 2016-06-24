<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Integer;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use PHPUnit\Framework\TestCase;

class IntegerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Integer
     */
    protected $parameterHandler;

    public function setUp()
    {
        $this->parameterHandler = new Integer();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Integer::getFormType
     */
    public function testGetFormType()
    {
        self::assertEquals(IntegerType::class, $this->parameterHandler->getFormType());
    }
}
