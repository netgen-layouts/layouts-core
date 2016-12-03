<?php

namespace Netgen\BlockManager\Tests\TestCase;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Validation;
use PHPUnit\Framework\TestCase;

abstract class ValidatorTestCase extends TestCase
{
    /**
     * @var \Symfony\Component\Validator\Context\ExecutionContextInterface
     */
    protected $executionContext;

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
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();

        $this->executionContext = new ExecutionContext(
            $validator,
            'root',
            $this->createMock(TranslatorInterface::class)
        );

        $this->validator = $this->getValidator();
        $this->validator->initialize($this->executionContext);
    }

    /**
     * @param bool $isValid
     * @param mixed $value
     */
    public function assertValid($isValid, $value)
    {
        $this->executionContext->setConstraint($this->constraint);
        $this->validator->validate($value, $this->constraint);

        if ($isValid) {
            $this->assertCount(0, $this->executionContext->getViolations());
        }

        if (!$isValid) {
            $this->assertGreaterThan(0, count($this->executionContext->getViolations()));
        }
    }

    /**
     * @return \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    abstract public function getValidator();
}
