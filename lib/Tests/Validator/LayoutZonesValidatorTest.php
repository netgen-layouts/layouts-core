<?php

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Validator\LayoutZonesValidator;
use Netgen\BlockManager\Validator\Constraint\LayoutZones;

class LayoutZonesValidatorTest extends ValidatorTest
{
    /**
     * @covers \Netgen\BlockManager\Validator\LayoutZonesValidator::__construct
     * @covers \Netgen\BlockManager\Validator\LayoutZonesValidator::validate
     */
    public function testValidate()
    {
        $this->configurationMock
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->equalTo('layouts'))
            ->will($this->returnValue(array('layout' => array('zones' => array('zone' => array())))));

        $this->executionContextMock
            ->expects($this->never())
            ->method('buildViolation');

        $validator = new LayoutZonesValidator($this->configurationMock);
        $validator->initialize($this->executionContextMock);

        $validator->validate(array('zone'), new LayoutZones(array('layoutIdentifier' => 'layout')));
    }

    /**
     * @covers \Netgen\BlockManager\Validator\LayoutZonesValidator::__construct
     * @covers \Netgen\BlockManager\Validator\LayoutZonesValidator::validate
     */
    public function testValidateFailedWithNoLayout()
    {
        $this->configurationMock
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->equalTo('layouts'))
            ->will($this->returnValue(array('layout' => array('zones' => array('zone' => array())))));

        $this->executionContextMock
            ->expects($this->once())
            ->method('buildViolation')
            ->will($this->returnValue($this->violationBuilderMock));

        $validator = new LayoutZonesValidator($this->configurationMock);
        $validator->initialize($this->executionContextMock);

        $validator->validate(array('zone'), new LayoutZones(array('layoutIdentifier' => 'other_layout')));
    }

    /**
     * @covers \Netgen\BlockManager\Validator\LayoutZonesValidator::__construct
     * @covers \Netgen\BlockManager\Validator\LayoutZonesValidator::validate
     */
    public function testValidateFailedWithInvalidZones()
    {
        $this->configurationMock
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->equalTo('layouts'))
            ->will($this->returnValue(array('layout' => array('zones' => array('zone' => array())))));

        $this->executionContextMock
            ->expects($this->once())
            ->method('buildViolation')
            ->will($this->returnValue($this->violationBuilderMock));

        $validator = new LayoutZonesValidator($this->configurationMock);
        $validator->initialize($this->executionContextMock);

        $validator->validate(42, new LayoutZones(array('layoutIdentifier' => 'layout')));
    }

    /**
     * @covers \Netgen\BlockManager\Validator\LayoutZonesValidator::__construct
     * @covers \Netgen\BlockManager\Validator\LayoutZonesValidator::validate
     */
    public function testValidateFailedWithExtraZone()
    {
        $this->configurationMock
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->equalTo('layouts'))
            ->will($this->returnValue(array('layout' => array('zones' => array('zone' => array())))));

        $this->executionContextMock
            ->expects($this->once())
            ->method('buildViolation')
            ->will($this->returnValue($this->violationBuilderMock));

        $validator = new LayoutZonesValidator($this->configurationMock);
        $validator->initialize($this->executionContextMock);

        $validator->validate(array('zone', 'other_zone'), new LayoutZones(array('layoutIdentifier' => 'layout')));
    }

    /**
     * @covers \Netgen\BlockManager\Validator\LayoutZonesValidator::__construct
     * @covers \Netgen\BlockManager\Validator\LayoutZonesValidator::validate
     */
    public function testValidateFailedWithMissingZone()
    {
        $this->configurationMock
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->equalTo('layouts'))
            ->will($this->returnValue(array('layout' => array('zones' => array('zone' => array())))));

        $this->executionContextMock
            ->expects($this->once())
            ->method('buildViolation')
            ->will($this->returnValue($this->violationBuilderMock));

        $validator = new LayoutZonesValidator($this->configurationMock);
        $validator->initialize($this->executionContextMock);

        $validator->validate(array(), new LayoutZones(array('layoutIdentifier' => 'layout')));
    }
}
