<?php

namespace Netgen\BlockManager\Tests\TestCase;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;
use PHPUnit\Framework\TestCase;

abstract class ValidatorTestCase extends TestCase
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
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    protected $validator;

    /**
     * @var \Symfony\Component\Validator\Constraint
     */
    protected $constraint;

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

        $this->validator = $this->getValidator();
        $this->validator->initialize($this->executionContextMock);

        $this->violationBuilderMock = $this
            ->createMock(ConstraintViolationBuilderInterface::class);

        $this->violationBuilderMock
            ->expects($this->any())
            ->method('setParameter')
            ->will($this->returnValue($this->violationBuilderMock));

        $this->violationBuilderMock
            ->expects($this->any())
            ->method('setCode')
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

    protected function expectValidate()
    {
        $this->executionContextMock
            ->expects($this->never())
            ->method('buildViolation');
    }

    protected function expectNoValidate()
    {
        $this->executionContextMock
            ->expects($this->atLeastOnce())
            ->method('buildViolation')
            ->will($this->returnValue($this->violationBuilderMock));
    }

    /**
     * @param bool $isValid
     * @param mixed $value
     */
    public function assertValid($isValid, $value)
    {
        $isValid ? $this->expectValidate() : $this->expectNoValidate();

        $this->validator->validate($value, $this->constraint);
    }

    /**
     * @return \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    abstract public function getValidator();
}
