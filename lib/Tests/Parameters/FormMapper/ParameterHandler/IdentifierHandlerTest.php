<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\IdentifierHandler;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use PHPUnit\Framework\TestCase;

class IdentifierTestHandler extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\IdentifierHandler
     */
    protected $parameterHandler;

    public function setUp()
    {
        $this->parameterHandler = new IdentifierHandler();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\IdentifierHandler::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(TextType::class, $this->parameterHandler->getFormType());
    }
}
