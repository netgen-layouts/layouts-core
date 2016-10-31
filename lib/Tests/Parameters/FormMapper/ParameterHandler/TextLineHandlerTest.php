<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\TextLineHandler;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use PHPUnit\Framework\TestCase;

class TextLineTestHandler extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\TextLineHandler
     */
    protected $parameterHandler;

    public function setUp()
    {
        $this->parameterHandler = new TextLineHandler();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\TextLineHandler::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(TextType::class, $this->parameterHandler->getFormType());
    }
}
