<?php

namespace Netgen\BlockManager\Validator\Parameters;

use Netgen\BlockManager\Parameters\Value\LinkValue;
use Netgen\BlockManager\Validator\Constraint\Parameters\ItemLink;
use Netgen\BlockManager\Validator\Constraint\Parameters\Link;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates if the provided value is a valid instance of
 * \Netgen\BlockManager\Parameters\Value\LinkValue object.
 */
final class LinkValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
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

        if ($linkType !== null) {
            $validator->atPath('linkType')->validate(
                $linkType,
                [
                    new Constraints\Choice(
                        [
                            'choices' => [
                                LinkValue::LINK_TYPE_URL,
                                LinkValue::LINK_TYPE_EMAIL,
                                LinkValue::LINK_TYPE_PHONE,
                                LinkValue::LINK_TYPE_INTERNAL,
                            ],
                            'strict' => true,
                        ]
                    ),
                ]
            );
        }

        $linkConstraints = [];
        if ($linkType === null) {
            $linkConstraints[] = new Constraints\IsNull();
        } elseif ($constraint->required) {
            $linkConstraints[] = new Constraints\NotBlank();
        }

        if ($linkType !== null) {
            if ($linkType === LinkValue::LINK_TYPE_URL) {
                $linkConstraints[] = new Constraints\Url();
            } elseif ($linkType === LinkValue::LINK_TYPE_EMAIL) {
                $linkConstraints[] = new Constraints\Email();
            } elseif ($linkType === LinkValue::LINK_TYPE_PHONE) {
                $linkConstraints[] = new Constraints\Type(['type' => 'string']);
            } elseif ($linkType === LinkValue::LINK_TYPE_INTERNAL) {
                $linkConstraints[] = new ItemLink(
                    [
                        'valueTypes' => $constraint->valueTypes,
                        'allowInvalid' => $constraint->allowInvalidInternal,
                    ]
                );
            }
        }

        $validator->atPath('link')->validate($value->getLink(), $linkConstraints);

        if ($value->getLinkSuffix() !== null) {
            $validator->atPath('linkSuffix')->validate(
                $value->getLinkSuffix(),
                [
                    new Constraints\Type(['type' => 'string']),
                ]
            );
        }

        $validator->atPath('newWindow')->validate(
            $value->getNewWindow(),
            [
                new Constraints\NotNull(),
                new Constraints\Type(['type' => 'bool']),
            ]
        );
    }
}
