<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\TestCase;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class ValidatorTestCase extends TestCase
{
    use ValidatorTestCaseTrait;

    final protected mixed $constraint;

    private ConstraintValidatorInterface $constraintValidator;

    private ExecutionContext $executionContext;

    protected function setUp(): void
    {
        $this->executionContext = new ExecutionContext(
            $this->createValidator(),
            'root',
            self::createStub(TranslatorInterface::class),
        );

        $this->constraintValidator = $this->getConstraintValidator();
        $this->constraintValidator->initialize($this->executionContext);
    }

    final protected function assertValid(bool $isValid, mixed $value): void
    {
        $this->executionContext->setConstraint($this->constraint);
        $this->constraintValidator->validate($value, $this->constraint);

        $isValid ?
            self::assertCount(0, $this->executionContext->getViolations()) :
            self::assertNotCount(0, $this->executionContext->getViolations());
    }

    abstract protected function getConstraintValidator(): ConstraintValidatorInterface;
}
