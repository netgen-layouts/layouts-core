<?php

namespace Netgen\BlockManager\Validator\Parameters;

use Netgen\BlockManager\Validator\Constraint\Parameters\Link;
use Netgen\BlockManager\Validator\Constraint\ItemLink;
use Netgen\BlockManager\Parameters\Value\Link as LinkValue;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;

class LinkValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param \Symfony\Component\Validator\Constraint $constraint The constraint for the validation
     */
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
                array(
                    new Constraints\Choice(
                        array(
                            'choices' => array(
                                LinkValue::LINK_TYPE_URL,
                                LinkValue::LINK_TYPE_EMAIL,
                                LinkValue::LINK_TYPE_PHONE,
                                LinkValue::LINK_TYPE_INTERNAL,
                            ),
                        )
                    ),
                )
            );
        }

        $linkConstraints = array(
            $linkType !== null ?
                new Constraints\NotBlank() :
                new Constraints\IsNull(),
        );

        if ($linkType !== null) {
            if ($linkType === LinkValue::LINK_TYPE_URL) {
                $linkConstraints[] = new Constraints\Url();
            } elseif ($linkType === LinkValue::LINK_TYPE_EMAIL) {
                $linkConstraints[] = new Constraints\Email();
            } elseif ($linkType === LinkValue::LINK_TYPE_PHONE) {
                $linkConstraints[] = new Constraints\Type(array('type' => 'string'));
            } elseif ($linkType === LinkValue::LINK_TYPE_INTERNAL) {
                $linkConstraints[] = new ItemLink(
                    array(
                        'valueTypes' => $constraint->valueTypes,
                    )
                );
            }
        }

        $validator->atPath('link')->validate($value->getLink(), $linkConstraints);

        if ($value->getLinkSuffix() !== null) {
            $validator->atPath('linkSuffix')->validate(
                $value->getLinkSuffix(),
                array(
                    new Constraints\Type(array('type' => 'string')),
                )
            );
        }

        $validator->atPath('newWindow')->validate(
            $value->getNewWindow(),
            array(
                new Constraints\NotNull(),
                new Constraints\Type(array('type' => 'bool')),
            )
        );
    }
}
