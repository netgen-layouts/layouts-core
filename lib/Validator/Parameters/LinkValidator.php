<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Parameters;

use Netgen\Layouts\Parameters\Value\LinkType;
use Netgen\Layouts\Parameters\Value\LinkValue;
use Netgen\Layouts\Validator\Constraint\Parameters\ItemLink;
use Netgen\Layouts\Validator\Constraint\Parameters\Link;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates if the provided value is a valid instance of
 * \Netgen\Layouts\Parameters\Value\LinkValue object.
 */
final class LinkValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if ($value === null) {
            return;
        }

        if (!$constraint instanceof Link) {
            throw new UnexpectedTypeException($constraint, Link::class);
        }

        if (!$value instanceof LinkValue) {
            throw new UnexpectedTypeException($value, LinkValue::class);
        }

        /** @var \Symfony\Component\Validator\Validator\ContextualValidatorInterface $validator */
        $validator = $this->context->getValidator()->inContext($this->context);

        $linkType = $value->getLinkType();

        $validator->atPath('linkType')->validate(
            $linkType,
            [
                new Constraints\Choice(
                    choices: [
                        null,
                        LinkType::Url,
                        LinkType::RelativeUrl,
                        LinkType::Email,
                        LinkType::Phone,
                        LinkType::Internal,
                    ],
                    strict: true,
                ),
            ],
        );

        $linkConstraints = [];
        if ($linkType === null) {
            $linkConstraints[] = new Constraints\IdenticalTo('');
        } elseif ($constraint->required) {
            $linkConstraints[] = new Constraints\NotBlank();
        }

        if ($linkType !== null) {
            $linkConstraints[] = new Constraints\NotNull();

            $linkConstraints[] = match ($linkType) {
                LinkType::Url => new Constraints\Url(requireTld: false),
                // @deprecated Replace with Url constraint with "relativeProtocol" option when support for Symfony 3.4 ends
                LinkType::RelativeUrl => new Constraints\Type(type: 'string'),
                LinkType::Email => new Constraints\Email(mode: Constraints\Email::VALIDATION_MODE_STRICT),
                LinkType::Phone => new Constraints\Type(type: 'string'),
                LinkType::Internal => new ItemLink(
                    [
                        'valueTypes' => $constraint->valueTypes,
                        'allowInvalid' => $constraint->allowInvalidInternal,
                    ],
                ),
            };
        }

        $validator->atPath('link')->validate($value->getLink(), $linkConstraints);

        $validator->atPath('linkSuffix')->validate(
            $value->getLinkSuffix(),
            [
                new Constraints\NotNull(),
                new Constraints\Type(type: 'string'),
            ],
        );

        $validator->atPath('newWindow')->validate(
            $value->getNewWindow(),
            [
                new Constraints\NotNull(),
                new Constraints\Type(type: 'bool'),
            ],
        );
    }
}
