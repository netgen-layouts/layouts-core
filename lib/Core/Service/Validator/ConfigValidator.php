<?php

namespace Netgen\BlockManager\Core\Service\Validator;

use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Exception\Validation\ValidationException;
use Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct as ParameterStructConstraint;
use Symfony\Component\Validator\Constraints;

final class ConfigValidator extends Validator
{
    /**
     * Validates the provided config structs according to provided config definitions.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterCollectionInterface[] $configStructs
     * @param \Netgen\BlockManager\Config\ConfigDefinitionInterface[] $configDefinitions
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateConfigStructs(array $configStructs, array $configDefinitions)
    {
        foreach ($configStructs as $configKey => $configStruct) {
            if (!isset($configDefinitions[$configKey])) {
                throw ValidationException::validationFailed(
                    'configStructs',
                    sprintf(
                        'Config definition with "%s" config key does not exist.',
                        $configKey
                    )
                );
            }
        }

        foreach ($configDefinitions as $configKey => $configDefinition) {
            if (!isset($configStructs[$configKey])) {
                continue;
            }

            $this->validate(
                $configStructs[$configKey],
                array(
                    new Constraints\Type(array('type' => ConfigStruct::class)),
                )
            );

            $this->validate(
                $configStructs[$configKey],
                array(
                    new ParameterStructConstraint(
                        array(
                            'parameterCollection' => $configDefinition,
                        )
                    ),
                ),
                'parameterValues'
            );
        }
    }
}
