<?php

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Netgen\BlockManager\Tests\Core\Service\Stubs\Validator;
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
        $this->validator = new Validator();
        $this->validator->setValidator($this->validatorMock);
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
                        new Constraints\Type(array('type' => 'scalar')),
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
                        new Constraints\Type(array('type' => 'string')),
                    )
                )
            )
            ->will($this->returnValue(new ConstraintViolationList()));

        $this->validator->validateIdentifier('identifier');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\Validator::validateIdentifier
     */
    public function testValidateRequiredIdentifier()
    {
        $this->validatorMock
            ->expects($this->once())
            ->method('validate')
            ->with(
                $this->equalTo('identifier'),
                $this->equalTo(
                    array(
                        new Constraints\Type(array('type' => 'string')),
                        new Constraints\NotBlank(),
                    )
                )
            )
            ->will($this->returnValue(new ConstraintViolationList()));

        $this->validator->validateIdentifier('identifier', null, true);
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
                        new Constraints\Type(array('type' => 'int')),
                    )
                )
            )
            ->will($this->returnValue(new ConstraintViolationList()));

        $this->validator->validatePosition(3);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\Validator::validatePosition
     */
    public function testValidateRequiredPosition()
    {
        $this->validatorMock
            ->expects($this->once())
            ->method('validate')
            ->with(
                $this->equalTo(3),
                $this->equalTo(
                    array(
                        new Constraints\GreaterThanOrEqual(0),
                        new Constraints\Type(array('type' => 'int')),
                        new Constraints\NotBlank(),
                    )
                )
            )
            ->will($this->returnValue(new ConstraintViolationList()));

        $this->validator->validatePosition(3, null, true);
    }
}
