<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Identifier;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use PHPUnit\Framework\TestCase;

class IdentifierTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Identifier
     */
    protected $parameterHandler;

    public function setUp()
    {
        $this->parameterHandler = new Identifier();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Identifier::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(TextType::class, $this->parameterHandler->getFormType());
    }
}
