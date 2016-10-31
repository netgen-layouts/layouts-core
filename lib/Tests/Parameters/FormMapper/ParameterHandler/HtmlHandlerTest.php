<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\HtmlHandler;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use PHPUnit\Framework\TestCase;

class HtmlTestHandler extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\HtmlHandler
     */
    protected $parameterHandler;

    public function setUp()
    {
        $this->parameterHandler = new HtmlHandler();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\HtmlHandler::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(TextareaType::class, $this->parameterHandler->getFormType());
    }
}
