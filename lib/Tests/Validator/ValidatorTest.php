<?php

namespace Netgen\BlockManager\Tests\Validator;

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
            'Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistryInterface'
        );

        $this->configurationMock = $this->getMock(
            'Netgen\BlockManager\Configuration\ConfigurationInterface'
        );

        $this->validatorMock = $this->getMock(
            'Symfony\Component\Validator\Validator\ValidatorInterface'
        );

        $this->constraintViolationMock = $this->getMock(
            'Symfony\Component\Validator\ConstraintViolationInterface'
        );

        $this->executionContextMock = $this->getMock(
            'Symfony\Component\Validator\Context\ExecutionContextInterface'
        );

        $this->violationBuilderMock = $this->getMockBuilder(
            'Symfony\Component\Validator\Violation\ConstraintViolationBuilder'
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
