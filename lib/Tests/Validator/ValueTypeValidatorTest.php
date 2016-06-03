<?php

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Item\Registry\ValueLoaderRegistry;
use Netgen\BlockManager\Tests\Item\Stubs\ValueLoader;
use Netgen\BlockManager\Validator\ValueTypeValidator;
use Netgen\BlockManager\Validator\Constraint\ValueType;

class ValueTypeValidatorTest extends ValidatorTest
{
    /**
     * @var \Netgen\BlockManager\Item\Registry\ValueLoaderRegistryInterface
     */
    protected $valueLoaderRegistry;

    /**
     * @var \Netgen\BlockManager\Validator\ValueTypeValidator
     */
    protected $validator;

    /**
     * @var \Netgen\BlockManager\Validator\Constraint\ValueType
     */
    protected $constraint;

    public function setUp()
    {
        parent::setUp();

        $this->valueLoaderRegistry = new ValueLoaderRegistry();
        $this->valueLoaderRegistry->addValueLoader(new ValueLoader());

        $this->validator = new ValueTypeValidator($this->valueLoaderRegistry);
        $this->validator->initialize($this->executionContextMock);

        $this->constraint = new ValueType();
    }

    /**
     * @param string $valueType
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Validator\ValueTypeValidator::__construct
     * @covers \Netgen\BlockManager\Validator\ValueTypeValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($valueType, $isValid)
    {
        if ($isValid) {
            $this->executionContextMock
                ->expects($this->never())
                ->method('buildViolation');
        } else {
            $this->executionContextMock
                ->expects($this->once())
                ->method('buildViolation')
                ->will($this->returnValue($this->violationBuilderMock));
        }

        $this->validator->validate($valueType, $this->constraint);
    }

    public function validateDataProvider()
    {
        return array(
            array('value', true),
            array('other', false),
            array('', false),
        );
    }
}
