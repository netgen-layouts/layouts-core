<?php

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType\TextType;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints;

final class ParameterTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterTypeInterface
     */
    private $parameterType;

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
            array(new Constraints\NotNull()),
            $this->parameterType->getConstraints(
                new ParameterDefinition(
                    array(
                        'type' => new ParameterType(),
                    )
                ),
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
            array(new Constraints\NotBlank(), new Constraints\NotNull()),
            $this->parameterType->getConstraints(
                new ParameterDefinition(
                    array(
                        'type' => new ParameterType(),
                        'isRequired' => true,
                    )
                ),
                42
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::getConstraints
     * @expectedException \Netgen\BlockManager\Exception\Parameters\ParameterTypeException
     * @expectedExceptionMessage Parameter with "text" type is not supported
     */
    public function testGetConstraintsThrowsParameterTypeException()
    {
        $this->parameterType->getConstraints(
            new ParameterDefinition(array('type' => new TextType())),
            42
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::toHash
     */
    public function testToHash()
    {
        $this->assertEquals(42, $this->parameterType->toHash(new ParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::fromHash
     */
    public function testFromHash()
    {
        $this->assertEquals(42, $this->parameterType->fromHash(new ParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::export
     */
    public function testExport()
    {
        $this->assertEquals(42, $this->parameterType->export(new ParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::import
     */
    public function testImport()
    {
        $this->assertEquals(42, $this->parameterType->import(new ParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::isValueEmpty
     */
    public function testIsValueEmpty()
    {
        $this->assertTrue($this->parameterType->isValueEmpty(new ParameterDefinition(), null));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType::isValueEmpty
     */
    public function testIsValueEmptyReturnsFalse()
    {
        $this->assertFalse($this->parameterType->isValueEmpty(new ParameterDefinition(), 42));
    }
}
