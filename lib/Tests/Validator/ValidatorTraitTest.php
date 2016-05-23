<?php

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Netgen\BlockManager\Tests\Validator\Stubs\ValidatorValue;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorTraitTest extends \PHPUnit_Framework_TestCase
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
        $this->validatorMock = $this->getMock(ValidatorInterface::class);
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
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     */
    public function testValidateThrowsInvalidArgumentException()
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
                            $this->getMock(
                                ConstraintViolationInterface::class
                            ),
                        )
                    )
                )
            );

        $this->validator->validate('some value', array(new Constraints\NotBlank()));
    }
}
