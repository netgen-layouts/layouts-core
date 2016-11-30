<?php

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Parameters\CompoundParameter;
use Netgen\BlockManager\Parameters\ParameterBuilder;
use Netgen\BlockManager\Parameters\ParameterType\Compound\BooleanType;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use Netgen\BlockManager\Tests\Parameters\Stubs\CompoundParameterType;
use Symfony\Component\Validator\Constraints\NotBlank;
use PHPUnit\Framework\TestCase;

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
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     */
    public function testGetConstraintsThrowsInvalidArgumentException()
    {
        $this->parameterType->getConstraints(
            new CompoundParameter(array('type' => new BooleanType())),
            42
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::fromValue
     */
    public function testFromValue()
    {
        $this->assertEquals(42, $this->parameterType->fromValue(42));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterType::toValue
     */
    public function testToValue()
    {
        $this->assertEquals(42, $this->parameterType->toValue(42));
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
