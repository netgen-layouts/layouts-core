<?php

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Validator\Constraint\Layout;
use Netgen\BlockManager\Validator\Constraint\LayoutZones;
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
        $this->layoutValidator = new LayoutValidator($this->validatorMock);
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
                    new Layout(),
                )
            )
            ->will($this->returnValue(new ConstraintViolationList()));

        $this->validatorMock
            ->expects($this->at(2))
            ->method('validate')
            ->with(
                $this->equalTo(array('left', 'right', 'bottom')),
                array(
                    new Constraints\NotBlank(),
                    new Constraints\Type(array('type' => 'array')),
                    new Constraints\All(
                        array(
                            'constraints' => array(
                                new Constraints\NotBlank(),
                                new Constraints\Type(array('type' => 'string')),
                            ),
                        )
                    ),
                    new LayoutZones(array('layoutIdentifier' => '3_zones_a')),
                )
            )
            ->will($this->returnValue(new ConstraintViolationList()));

        $layoutCreateStruct = new LayoutCreateStruct();
        $layoutCreateStruct->name = 'My layout';
        $layoutCreateStruct->identifier = '3_zones_a';
        $layoutCreateStruct->zoneIdentifiers = array('left', 'right', 'bottom');

        $this->layoutValidator->validateLayoutCreateStruct($layoutCreateStruct);
    }
}
