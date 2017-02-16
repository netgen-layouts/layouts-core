<?php

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Exception;
use Netgen\BlockManager\Tests\Validator\Stubs\ValidatorValue;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorTraitTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $validatorMock;

    /**
     * @var \Netgen\BlockManager\Tests\Validator\Stubs\ValidatorValue
     */
    protected $validator;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        $this->validatorMock = $this->createMock(ValidatorInterface::class);
        $this->validator = new ValidatorValue();
        $this->validator->setValidator($this->validatorMock);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\ValidatorTrait::validate
     * @covers \Netgen\BlockManager\Validator\ValidatorTrait::setValidator
     */
    public function testValidate()
    {
        $this->validatorMock
            ->expects($this->once())
            ->method('validate')
            ->with(
                $this->equalTo('some value'),
                $this->equalTo(array(new Constraints\NotBlank()))
            )
            ->will($this->returnValue(new ConstraintViolationList()));

        $this->validator->validate('some value', array(new Constraints\NotBlank()));
    }

    /**
     * @covers \Netgen\BlockManager\Validator\ValidatorTrait::validate
     * @expectedException \Netgen\BlockManager\Exception\ValidationFailedException
     * @expectedExceptionMessage There was an error validating "value": Value should not be blank
     */
    public function testValidateThrowsValidationFailedException()
    {
        $this->validatorMock
            ->expects($this->once())
            ->method('validate')
            ->with(
                $this->equalTo('some value'),
                $this->equalTo(array(new Constraints\NotBlank()))
            )->will(
                $this->returnValue(
                    new ConstraintViolationList(
                        array(
                            $this->createConfiguredMock(
                                ConstraintViolationInterface::class,
                                array('getMessage' => 'Value should not be blank')
                            ),
                        )
                    )
                )
            );

        $this->validator->validate('some value', array(new Constraints\NotBlank()), 'value');
    }

    /**
     * @covers \Netgen\BlockManager\Validator\ValidatorTrait::validate
     * @expectedException \Netgen\BlockManager\Exception\ValidationFailedException
     * @expectedExceptionMessage Test exception text
     */
    public function testValidateThrowsValidationFailedExceptionOnOtherException()
    {
        $this->validatorMock
            ->expects($this->once())
            ->method('validate')
            ->will(
                $this->throwException(new Exception('Test exception text'))
            );

        $this->validator->validate('some value', array(new Constraints\NotBlank()));
    }
}
