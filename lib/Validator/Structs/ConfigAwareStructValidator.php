<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Validator\Structs;

use Netgen\BlockManager\API\Values\Config\ConfigAwareStruct;
use Netgen\BlockManager\Config\ConfigDefinitionAwareInterface;
use Netgen\BlockManager\Config\ConfigDefinitionInterface;
use Netgen\BlockManager\Validator\Constraint\Structs\ConfigAwareStruct as ConfigAwareStructConstraint;
use Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates the complete value which implements ConfigAwareStruct interface.
 */
final class ConfigAwareStructValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ConfigAwareStructConstraint) {
            throw new UnexpectedTypeException($constraint, ConfigAwareStructConstraint::class);
        }

        if (!$constraint->payload instanceof ConfigDefinitionAwareInterface && !is_array($constraint->payload)) {
            throw new UnexpectedTypeException(
                $constraint->payload,
                sprintf('%s or %s', ConfigDefinitionAwareInterface::class, 'array')
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
            if (!isset($configDefinitions[$configKey]) || !$configDefinitions[$configKey] instanceof ConfigDefinitionInterface) {
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
                        ]
                    ),
                ]
            );
        }
    }
}
