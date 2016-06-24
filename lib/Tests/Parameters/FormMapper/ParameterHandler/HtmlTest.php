<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Html;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use PHPUnit\Framework\TestCase;

class HtmlTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Text
     */
    protected $parameterHandler;

    public function setUp()
    {
        $this->parameterHandler = new Html();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Html::getFormType
     */
    public function testGetFormType()
    {
        self::assertEquals(TextareaType::class, $this->parameterHandler->getFormType());
    }
}
