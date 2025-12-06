<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\TestCase;

use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\Item\CmsItemLoaderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class ValidatorTestCase extends TestCase
{
    final protected mixed $constraint;

    private ExecutionContext $executionContext;

    private ConstraintValidatorInterface $validator;

    protected function setUp(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(
                new ValidatorFactory(
                    self::createStub(LayoutService::class),
                    self::createStub(LayoutResolverService::class),
                    self::createStub(CmsItemLoaderInterface::class),
                ),
            )
            ->getValidator();

        $this->executionContext = new ExecutionContext(
            $validator,
            'root',
            self::createStub(TranslatorInterface::class),
        );

        $this->validator = $this->getValidator();
        $this->validator->initialize($this->executionContext);
    }

    final protected function assertValid(bool $isValid, mixed $value): void
    {
        $this->executionContext->setConstraint($this->constraint);
        $this->validator->validate($value, $this->constraint);

        $isValid ?
            self::assertCount(0, $this->executionContext->getViolations()) :
            self::assertNotCount(0, $this->executionContext->getViolations());
    }

    abstract protected function getValidator(): ConstraintValidatorInterface;
}
