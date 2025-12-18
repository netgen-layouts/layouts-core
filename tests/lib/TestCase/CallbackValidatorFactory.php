<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\TestCase;

use Closure;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Validator\ConstraintValidatorInterface;

use function str_starts_with;

final class CallbackValidatorFactory implements ConstraintValidatorFactoryInterface
{
    private Closure $callback;

    private ConstraintValidatorFactoryInterface $symfonyValidatorFactory;

    public function __construct(callable $callback)
    {
        $this->callback = $callback(...);
        $this->symfonyValidatorFactory = new ConstraintValidatorFactory();
    }

    public function getInstance(Constraint $constraint): ConstraintValidatorInterface
    {
        if (str_starts_with($constraint->validatedBy(), 'nglayouts_')) {
            return ($this->callback)($constraint);
        }

        return $this->symfonyValidatorFactory->getInstance($constraint);
    }
}
