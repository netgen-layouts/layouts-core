<?php

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\ParameterType\TextType;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterType;
use Symfony\Component\Validator\Constraints\NotBlank;
use PHPUnit\Framework\TestCase;

class ParameterTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterTypeInterface
     */
    protected $parameterType;

    public function setUp()
    {
        $this->parameterType = new ParameterType();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::getConstraints
     * @covers \Netgen\BlockManager\Parameters\ParameterType::getRequiredConstraints
     * @covers \Netgen\BlockManager\Parameters\ParameterType::getValueConstraints
     */
    public function testGetConstraints()
    {
        $this->assertEquals(
            array(),
            $this->parameterType->getConstraints(
                new Parameter('name', new ParameterType()),
                42
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::getConstraints
     * @covers \Netgen\BlockManager\Parameters\ParameterType::getRequiredConstraints
     * @covers \Netgen\BlockManager\Parameters\ParameterType::getValueConstraints
     */
    public function testGetConstraintsWithRequiredParameter()
    {
        $this->assertEquals(
            array(new NotBlank()),
            $this->parameterType->getConstraints(
                new Parameter('name', new ParameterType(), array('required' => true)),
                42
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::getConstraints
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     */
    public function testGetConstraintsThrowsInvalidArgumentException()
    {
        $this->parameterType->getConstraints(
            new Parameter('name', new TextType()),
            42
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::fromValue
     */
    public function testFromValue()
    {
        $this->assertEquals(42, $this->parameterType->fromValue(42));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::toValue
     */
    public function testToValue()
    {
        $this->assertEquals(42, $this->parameterType->toValue(42));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::isValueEmpty
     */
    public function testIsValueEmpty()
    {
        $this->assertTrue($this->parameterType->isValueEmpty(null));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::isValueEmpty
     */
    public function testIsValueEmptyReturnsFalse()
    {
        $this->assertFalse($this->parameterType->isValueEmpty(42));
    }
}
