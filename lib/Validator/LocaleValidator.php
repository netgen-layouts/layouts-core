<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator;

use Locale;
use Netgen\Layouts\Validator\Constraint\Locale as LocaleConstraint;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use function is_string;

/**
 * Validates if the locale is a valid one, while disallowing the non-canonicalized versions
 * of the locale string.
 *
 * This validator exists due to Symfony 4.1+ throwing a deprecation if non-canonicalized versions
 * of locales are not allowed.
 */
final class LocaleValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof LocaleConstraint) {
            throw new UnexpectedTypeException($constraint, LocaleConstraint::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        /** @var \Symfony\Component\Validator\Validator\ContextualValidatorInterface $validator */
        $validator = $this->context->getValidator()->inContext($this->context);

        $localeConstraint = Kernel::VERSION_ID >= 40100 ?
            new Constraints\Locale(['canonicalize' => true]) :
            new Constraints\Locale();

        $validator->validate($value, [$localeConstraint]);

        $canonicalizedLocale = Locale::canonicalize($value);

        if ($canonicalizedLocale !== $value) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
