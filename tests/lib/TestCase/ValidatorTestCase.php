<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\TestCase;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Validation;

abstract class ValidatorTestCase extends TestCase
{
    /**
     * @var mixed
     */
    protected $constraint;

    private ExecutionContext $executionContext;

    private ConstraintValidatorInterface $validator;

    protected function setUp(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();

        $this->executionContext = new ExecutionContext($validator, 'root', new Translator('en'));

        $this->validator = $this->getValidator();
        $this->validator->initialize($this->executionContext);
    }

    /**
     * @param mixed $value
     */
    protected function assertValid(bool $isValid, $value): void
    {
        $this->executionContext->setConstraint($this->constraint);
        $this->validator->validate($value, $this->constraint);

        $isValid ?
            self::assertCount(0, $this->executionContext->getViolations()) :
            self::assertNotCount(0, $this->executionContext->getViolations());
    }

    abstract protected function getValidator(): ConstraintValidatorInterface;
}
