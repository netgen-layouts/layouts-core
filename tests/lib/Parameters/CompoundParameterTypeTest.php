<?php

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Parameters\CompoundParameter;
use Netgen\BlockManager\Parameters\ParameterBuilder;
use Netgen\BlockManager\Parameters\ParameterType\Compound\BooleanType;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use Netgen\BlockManager\Tests\Parameters\Stubs\CompoundParameterType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotBlank;

class CompoundParameterTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\CompoundParameterTypeInterface
     */
    protected $parameterType;

    public function setUp()
    {
        $this->parameterType = new CompoundParameterType();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::buildParameters
     */
    public function testBuildParameters()
    {
        $parameterBuilder = new ParameterBuilder(new ParameterTypeRegistry());

        $this->parameterType->buildParameters($parameterBuilder);

        $this->assertCount(0, $parameterBuilder);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::getConstraints
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::getRequiredConstraints
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::getValueConstraints
     */
    public function testGetConstraints()
    {
        $this->assertEquals(
            array(),
            $this->parameterType->getConstraints(
                new CompoundParameter(
                    array(
                        'type' => new CompoundParameterType(),
                    )
                ),
                42
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::getConstraints
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::getRequiredConstraints
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::getValueConstraints
     */
    public function testGetConstraintsWithRequiredParameter()
    {
        $this->assertEquals(
            array(new NotBlank()),
            $this->parameterType->getConstraints(
                new CompoundParameter(
                    array(
                        'type' => new CompoundParameterType(),
                        'isRequired' => true,
                    )
                ),
                42
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::getConstraints
     * @expectedException \Netgen\BlockManager\Exception\Parameters\ParameterTypeException
     * @expectedExceptionMessage Parameter with "compound_boolean" type is not supported
     */
    public function testGetConstraintsThrowsParameterTypeException()
    {
        $this->parameterType->getConstraints(
            new CompoundParameter(array('type' => new BooleanType())),
            42
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::toHash
     */
    public function testToHash()
    {
        $this->assertEquals(42, $this->parameterType->toHash(42));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::fromHash
     */
    public function testFromHash()
    {
        $this->assertEquals(42, $this->parameterType->fromHash(42));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::createValueFromInput
     */
    public function testCreateValueFromInput()
    {
        $this->assertEquals(42, $this->parameterType->createValueFromInput(42));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::isValueEmpty
     */
    public function testIsValueEmpty()
    {
        $this->assertTrue($this->parameterType->isValueEmpty(null));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::isValueEmpty
     */
    public function testIsValueEmptyReturnsFalse()
    {
        $this->assertFalse($this->parameterType->isValueEmpty(42));
    }
}
