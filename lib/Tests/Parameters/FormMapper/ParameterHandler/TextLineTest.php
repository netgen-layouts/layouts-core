<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\TextLine;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use PHPUnit\Framework\TestCase;

class TextLineTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\TextLine
     */
    protected $parameterHandler;

    public function setUp()
    {
        $this->parameterHandler = new TextLine();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\TextLine::getFormType
     */
    public function testGetFormType()
    {
        self::assertEquals(TextType::class, $this->parameterHandler->getFormType());
    }
}
