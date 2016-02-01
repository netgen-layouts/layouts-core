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
     * Sets up the test.
     */
    public function setUp()
    {
        $this->validatorMock = $this->getMock(
            ValidatorInterface::class
        );
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

        $layoutValidator = new Validator($this->validatorMock);
        $layoutValidator->validate('some value', array(new Constraints\NotBlank()));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\Validator::__construct
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

        $layoutValidator = new Validator($this->validatorMock);
        $layoutValidator->validate('some value', array(new Constraints\NotBlank()));
    }
}
