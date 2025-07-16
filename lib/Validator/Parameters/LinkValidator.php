<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Parameters;

use Netgen\Layouts\Parameters\Value\LinkValue;
use Netgen\Layouts\Validator\Constraint\Parameters\ItemLink;
use Netgen\Layouts\Validator\Constraint\Parameters\Link;
use Netgen\Layouts\Validator\StrictEmailValidatorTrait;
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
    use StrictEmailValidatorTrait;

    /**
     * @param mixed $value
     */
    public function validate($value, Constraint $constraint): void
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
                    [
                        'choices' => [
                            '',
                            LinkValue::LINK_TYPE_URL,
                            LinkValue::LINK_TYPE_RELATIVE_URL,
                            LinkValue::LINK_TYPE_EMAIL,
                            LinkValue::LINK_TYPE_PHONE,
                            LinkValue::LINK_TYPE_INTERNAL,
                        ],
                        'strict' => true,
                    ],
                ),
            ],
        );

        $linkConstraints = [];
        if ($linkType === '') {
            $linkConstraints[] = new Constraints\IdenticalTo('');
        } elseif ($constraint->required) {
            $linkConstraints[] = new Constraints\NotBlank();
        }

        if ($linkType !== '') {
            $linkConstraints[] = new Constraints\NotNull();

            if ($linkType === LinkValue::LINK_TYPE_URL) {
                $linkConstraints[] = new Constraints\Url();
            } elseif ($linkType === LinkValue::LINK_TYPE_RELATIVE_URL) {
                // @deprecated Replace with Url constraint with "relativeProtocol" option when support for Symfony 3.4 ends
                $linkConstraints[] = new Constraints\Type(['type' => 'string']);
            } elseif ($linkType === LinkValue::LINK_TYPE_EMAIL) {
                $linkConstraints[] = new Constraints\Email($this->getStrictEmailValidatorOption());
            } elseif ($linkType === LinkValue::LINK_TYPE_PHONE) {
                $linkConstraints[] = new Constraints\Type(['type' => 'string']);
            } elseif ($linkType === LinkValue::LINK_TYPE_INTERNAL) {
                $linkConstraints[] = new ItemLink(
                    [
                        'valueTypes' => $constraint->valueTypes,
                        'allowInvalid' => $constraint->allowInvalidInternal,
                    ],
                );
            }
        }

        $validator->atPath('link')->validate($value->getLink(), $linkConstraints);

        $validator->atPath('linkSuffix')->validate(
            $value->getLinkSuffix(),
            [
                new Constraints\NotNull(),
                new Constraints\Type(['type' => 'string']),
            ],
        );

        $validator->atPath('newWindow')->validate(
            $value->getNewWindow(),
            [
                new Constraints\NotNull(),
                new Constraints\Type(['type' => 'bool']),
            ],
        );
    }
}
