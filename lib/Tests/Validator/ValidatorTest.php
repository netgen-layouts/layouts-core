<?php

namespace Netgen\BlockManager\Tests\Validator;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validation;
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
        $this->executionContextMock = $this->createMock(ExecutionContextInterface::class);

        $this->executionContextMock
            ->expects($this->any())
            ->method('getValidator')
            ->will(
                $this->returnCallback(
                    function () {
                        return Validation::createValidatorBuilder()
                            ->setConstraintValidatorFactory(new ValidatorFactory())
                            ->getValidator();
                    }
                )
            );

        $this->violationBuilderMock = $this
            ->createMock(ConstraintViolationBuilderInterface::class);

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
