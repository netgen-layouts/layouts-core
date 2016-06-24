<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Text;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use PHPUnit\Framework\TestCase;

class TextTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Text
     */
    protected $parameterHandler;

    public function setUp()
    {
        $this->parameterHandler = new Text();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Text::getFormType
     */
    public function testGetFormType()
    {
        self::assertEquals(TextareaType::class, $this->parameterHandler->getFormType());
    }
}
