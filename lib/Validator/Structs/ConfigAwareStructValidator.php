<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Structs;

use Netgen\Layouts\API\Values\Config\ConfigAwareStruct;
use Netgen\Layouts\Config\ConfigDefinitionAwareInterface;
use Netgen\Layouts\Config\ConfigDefinitionInterface;
use Netgen\Layouts\Validator\Constraint\Structs\ConfigAwareStruct as ConfigAwareStructConstraint;
use Netgen\Layouts\Validator\Constraint\Structs\ParameterStruct;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use function is_array;
use function sprintf;

/**
 * Validates the complete value which implements ConfigAwareStruct interface.
 */
final class ConfigAwareStructValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ConfigAwareStructConstraint) {
            throw new UnexpectedTypeException($constraint, ConfigAwareStructConstraint::class);
        }

        if (!$constraint->payload instanceof ConfigDefinitionAwareInterface && !is_array($constraint->payload)) {
            throw new UnexpectedTypeException(
                $constraint->payload,
                sprintf('%s or %s', ConfigDefinitionAwareInterface::class, 'array'),
            );
        }

        if (!$value instanceof ConfigAwareStruct) {
            throw new UnexpectedTypeException($value, ConfigAwareStruct::class);
        }

        /** @var \Symfony\Component\Validator\Validator\ContextualValidatorInterface $validator */
        $validator = $this->context->getValidator()->inContext($this->context);

        $configDefinitions = !is_array($constraint->payload) ?
            $constraint->payload->getConfigDefinitions() :
            $constraint->payload;

        foreach ($value->getConfigStructs() as $configKey => $configStruct) {
            if (!($configDefinitions[$configKey] ?? null) instanceof ConfigDefinitionInterface) {
                $this->context->buildViolation($constraint->noConfigDefinitionMessage)
                    ->setParameter('%configKey%', $configKey)
                    ->addViolation();

                return;
            }

            $validator->atPath('configStructs[' . $configKey . '].parameterValues')->validate(
                $configStruct,
                [
                    new ParameterStruct(
                        [
                            'parameterDefinitions' => $configDefinitions[$configKey],
                            'allowMissingFields' => $constraint->allowMissingFields,
                        ],
                    ),
                ],
            );
        }
    }
}
