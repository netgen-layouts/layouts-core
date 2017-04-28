<?php

namespace Netgen\BlockManager\Core\Service\Validator;

use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Config\Registry\ConfigDefinitionRegistryInterface;
use Netgen\BlockManager\Exception\Validation\ValidationException;
use Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct as ParameterStructConstraint;
use Symfony\Component\Validator\Constraints;

class ConfigValidator extends Validator
{
    /**
     * @var \Netgen\BlockManager\Config\Registry\ConfigDefinitionRegistryInterface
     */
    protected $configDefinitionRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Config\Registry\ConfigDefinitionRegistryInterface $configDefinitionRegistry
     */
    public function __construct(ConfigDefinitionRegistryInterface $configDefinitionRegistry)
    {
        $this->configDefinitionRegistry = $configDefinitionRegistry;
    }

    /**
     * Validates config structs.
     *
     * @param string $type
     * @param \Netgen\BlockManager\Parameters\ParameterCollectionInterface[] $configStructs
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateConfigStructs($type, array $configStructs)
    {
        foreach ($configStructs as $identifier => $configStruct) {
            if (!$this->configDefinitionRegistry->hasConfigDefinition($type, $identifier)) {
                throw ValidationException::validationFailed(
                    'configStructs',
                    sprintf(
                        '"%s" config definition is not defined for "%s" type.',
                        $identifier,
                        $type
                    )
                );
            }
        }

        $configDefinitions = $this->configDefinitionRegistry->getConfigDefinitions($type);
        foreach ($configDefinitions as $identifier => $configDefinition) {
            if (!isset($configStructs[$identifier])) {
                continue;
            }

            $this->validate(
                $configStructs[$identifier],
                array(
                    new Constraints\Type(array('type' => ConfigStruct::class)),
                )
            );

            $this->validate(
                $configStructs[$identifier],
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
