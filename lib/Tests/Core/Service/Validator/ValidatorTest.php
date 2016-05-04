<?php

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Netgen\BlockManager\Tests\Core\Service\Stubs\Validator;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $validatorMock;

    /**
     * @var \Netgen\BlockManager\Tests\Core\Service\Stubs\Validator
     */
    protected $validator;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        $this->validatorMock = $this->getMock(ValidatorInterface::class);
        $this->validator = new Validator($this->validatorMock);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\Validator::__construct
     * @covers \Netgen\BlockManager\Core\Service\Validator\Validator::validate
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
     * @covers \Netgen\BlockManager\Core\Service\Validator\Validator::validate
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
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

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\Validator::validateId
     */
    public function testValidateId()
    {
        $this->validatorMock
            ->expects($this->once())
            ->method('validate')
            ->with(
                $this->equalTo(42),
                $this->equalTo(
                    array(
                        new Constraints\NotBlank(),
                        new Constraints\Type(array('type' => 'scalar'))
                    )
                )
            )
            ->will($this->returnValue(new ConstraintViolationList()));

        $this->validator->validateId(42);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\Validator::validateIdentifier
     */
    public function testValidateIdentifier()
    {
        $this->validatorMock
            ->expects($this->once())
            ->method('validate')
            ->with(
                $this->equalTo('identifier'),
                $this->equalTo(
                    array(
                        new Constraints\NotBlank(),
                        new Constraints\Type(array('type' => 'string'))
                    )
                )
            )
            ->will($this->returnValue(new ConstraintViolationList()));

        $this->validator->validateIdentifier('identifier');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\Validator::validatePosition
     */
    public function testValidatePosition()
    {
        $this->validatorMock
            ->expects($this->once())
            ->method('validate')
            ->with(
                $this->equalTo(3),
                $this->equalTo(
                    array(
                        new Constraints\GreaterThanOrEqual(0),
                        new Constraints\Type(array('type' => 'int'))
                    )
                )
            )
            ->will($this->returnValue(new ConstraintViolationList()));

        $this->validator->validatePosition(3);
    }
}
