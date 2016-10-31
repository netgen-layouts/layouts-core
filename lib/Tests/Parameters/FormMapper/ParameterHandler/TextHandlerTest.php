<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\TextHandler;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use PHPUnit\Framework\TestCase;

class TextTestHandler extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\TextHandler
     */
    protected $parameterHandler;

    public function setUp()
    {
        $this->parameterHandler = new TextHandler();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\TextHandler::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(TextareaType::class, $this->parameterHandler->getFormType());
    }
}
