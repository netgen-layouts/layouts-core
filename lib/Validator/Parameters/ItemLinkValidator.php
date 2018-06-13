<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Validator\Parameters;

use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\NullItem;
use Netgen\BlockManager\Validator\Constraint\Parameters\ItemLink;
use Netgen\BlockManager\Validator\Constraint\ValueType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates if the provided value is a valid link to an item.
 */
final class ItemLinkValidator extends ConstraintValidator
{
    /**
     * @var \Netgen\BlockManager\Item\ItemLoaderInterface
     */
    private $itemLoader;

    public function __construct(ItemLoaderInterface $itemLoader)
    {
        $this->itemLoader = $itemLoader;
    }

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

        if (empty($parsedValue['scheme']) || (empty($parsedValue['host']) && $parsedValue['host'] !== '0')) {
            $this->context->buildViolation($constraint->invalidItemMessage)
                ->addViolation();

            return;
        }

        if (!$constraint->allowInvalid) {
            $valueType = str_replace('-', '_', $parsedValue['scheme']);
            $itemValue = $parsedValue['host'];

            $validator->validate($valueType, new ValueType());
            if (count($validator->getViolations()) > 0) {
                return;
            }

            if (!empty($constraint->valueTypes) && is_array($constraint->valueTypes)) {
                if (!in_array($valueType, $constraint->valueTypes, true)) {
                    $this->context->buildViolation($constraint->valueTypeNotAllowedMessage)
                        ->setParameter('%valueType%', $valueType)
                        ->addViolation();

                    return;
                }
            }

            $item = $this->itemLoader->load($itemValue, $valueType);
            if ($item instanceof NullItem) {
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
            }
        }
    }
}
