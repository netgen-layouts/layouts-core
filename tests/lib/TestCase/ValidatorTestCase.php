<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\TestCase;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Validation;

abstract class ValidatorTestCase extends TestCase
{
    /**
     * @var mixed
     */
    protected $constraint;
    /**
     * @var \Symfony\Component\Validator\Context\ExecutionContextInterface
     */
    private $executionContext;

    /**
     * @var \Symfony\Component\Validator\ConstraintValidatorInterface
     */
    private $validator;

    public function setUp(): void
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
    public function assertValid(bool $isValid, $value): void
    {
        $this->executionContext->setConstraint($this->constraint);
        $this->validator->validate($value, $this->constraint);

        $isValid ?
            $this->assertCount(0, $this->executionContext->getViolations()) :
            $this->assertNotCount(0, $this->executionContext->getViolations());
    }

    abstract public function getValidator(): ConstraintValidatorInterface;
}
