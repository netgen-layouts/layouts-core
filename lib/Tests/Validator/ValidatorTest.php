<?php

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

abstract class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockDefinitionRegistryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configurationMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $validatorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $constraintViolationMock;

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
        $this->blockDefinitionRegistryMock = $this->getMock(
            BlockDefinitionRegistryInterface::class
        );

        $this->configurationMock = $this->getMock(ConfigurationInterface::class);

        $this->validatorMock = $this->getMock(ValidatorInterface::class);

        $this->constraintViolationMock = $this->getMock(ConstraintViolationInterface::class);

        $this->executionContextMock = $this->getMock(ExecutionContextInterface::class);

        $this->violationBuilderMock = $this->getMockBuilder(
            ConstraintViolationBuilderInterface::class
        )
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
