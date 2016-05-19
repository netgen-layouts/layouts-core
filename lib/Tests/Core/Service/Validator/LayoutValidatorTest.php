<?php

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LayoutValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $validatorMock;

    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\LayoutValidator
     */
    protected $layoutValidator;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        $this->validatorMock = $this->getMock(ValidatorInterface::class);
        $this->layoutValidator = new LayoutValidator();
        $this->layoutValidator->setValidator($this->validatorMock);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\LayoutValidator::validateLayoutCreateStruct
     */
    public function testValidateLayoutCreateStruct()
    {
        $this->validatorMock
            ->expects($this->at(0))
            ->method('validate')
            ->with(
                $this->equalTo('My layout'),
                array(
                    new Constraints\NotBlank(),
                    new Constraints\Type(array('type' => 'string')),
                )
            )
            ->will($this->returnValue(new ConstraintViolationList()));

        $this->validatorMock
            ->expects($this->at(1))
            ->method('validate')
            ->with(
                $this->equalTo('3_zones_a'),
                array(
                    new Constraints\NotBlank(),
                    new Constraints\Type(array('type' => 'string')),
                )
            )
            ->will($this->returnValue(new ConstraintViolationList()));

        $layoutCreateStruct = new LayoutCreateStruct();
        $layoutCreateStruct->name = 'My layout';
        $layoutCreateStruct->type = '3_zones_a';

        $this->layoutValidator->validateLayoutCreateStruct($layoutCreateStruct);
    }
}
