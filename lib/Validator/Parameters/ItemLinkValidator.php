<?php

namespace Netgen\BlockManager\Validator\Parameters;

use Netgen\BlockManager\Exception\InvalidItemException;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Validator\Constraint\Parameters\ItemLink;
use Netgen\BlockManager\Validator\Constraint\ValueType;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class ItemLinkValidator extends ConstraintValidator
{
    /**
     * @var \Netgen\BlockManager\Item\ItemLoaderInterface
     */
    protected $itemLoader;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Item\ItemLoaderInterface $itemLoader
     */
    public function __construct(ItemLoaderInterface $itemLoader)
    {
        $this->itemLoader = $itemLoader;
    }

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

        if (!$constraint instanceof ItemLink) {
            throw new UnexpectedTypeException($constraint, ItemLink::class);
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        /** @var \Symfony\Component\Validator\Validator\ContextualValidatorInterface $validator */
        $validator = $this->context->getValidator()->inContext($this->context);

        $parsedValue = parse_url($value);

        if (empty($parsedValue['scheme']) || empty($parsedValue['host'])) {
            $this->context->buildViolation($constraint->invalidItemMessage)
                ->addViolation();

            return;
        }

        $valueType = str_replace('-', '_', $parsedValue['scheme']);
        $valueId = $parsedValue['host'];

        $validator->validate($valueType, new ValueType());
        if (count($validator->getViolations()) > 0) {
            return;
        }

        if (!empty($constraint->valueTypes) && is_array($constraint->valueTypes)) {
            if (!in_array($valueType, $constraint->valueTypes)) {
                $this->context->buildViolation($constraint->valueTypeNotAllowedMessage)
                    ->setParameter('%valueType%', $valueType)
                    ->addViolation();

                return;
            }
        }

        try {
            $this->itemLoader->load($valueId, $valueType);
        } catch (InvalidItemException $e) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
