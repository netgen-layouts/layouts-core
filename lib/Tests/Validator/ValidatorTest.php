<?php

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

abstract class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $executionContextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $violationBuilderMock;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        $this->executionContextMock = $this->getMock(ExecutionContextInterface::class);

        $this->violationBuilderMock = $this
            ->getMockBuilder(ConstraintViolationBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->violationBuilderMock
            ->expects($this->any())
            ->method('setParameter')
            ->will($this->returnValue($this->violationBuilderMock));

        $this->violationBuilderMock
            ->expects($this->any())
            ->method('setInvalidValue')
            ->will($this->returnValue($this->violationBuilderMock));

        $this->violationBuilderMock
            ->expects($this->any())
            ->method('atPath')
            ->will($this->returnValue($this->violationBuilderMock));
    }
}
