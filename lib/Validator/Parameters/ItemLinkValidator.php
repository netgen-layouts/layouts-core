<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Parameters;

use Netgen\Layouts\Item\CmsItemLoaderInterface;
use Netgen\Layouts\Item\NullCmsItem;
use Netgen\Layouts\Validator\Constraint\Parameters\ItemLink;
use Netgen\Layouts\Validator\Constraint\ValueType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates if the provided value is a valid link to an item.
 */
final class ItemLinkValidator extends ConstraintValidator
{
    /**
     * @var \Netgen\Layouts\Item\CmsItemLoaderInterface
     */
    private $cmsItemLoader;

    public function __construct(CmsItemLoaderInterface $cmsItemLoader)
    {
        $this->cmsItemLoader = $cmsItemLoader;
    }

    public function validate($value, Constraint $constraint): void
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

        if (!is_array($parsedValue) || ($parsedValue['scheme'] ?? '') === '' || !isset($parsedValue['host'])) {
            $this->context->buildViolation($constraint->invalidItemMessage)
                ->addViolation();

            return;
        }

        if (!$constraint->allowInvalid) {
            $valueType = str_replace('-', '_', $parsedValue['scheme']);
            $itemValue = $parsedValue['host'];

            $validator->validate($valueType, new ValueType());
            if (count($validator->getViolations()) > 0) {
                // Validation constraint is already added to the validator
                // by the ValueTypeValidator
                return;
            }

            if (is_array($constraint->valueTypes) && count($constraint->valueTypes) > 0) {
                if (!in_array($valueType, $constraint->valueTypes, true)) {
                    $this->context->buildViolation($constraint->valueTypeNotAllowedMessage)
                        ->setParameter('%valueType%', $valueType)
                        ->addViolation();

                    return;
                }
            }

            $item = $this->cmsItemLoader->load($itemValue, $valueType);
            if ($item instanceof NullCmsItem) {
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
            }
        }
    }
}
