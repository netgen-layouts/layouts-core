<?php

namespace Netgen\BlockManager\Core\Service\Validator;

use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Config\Registry\ConfigDefinitionRegistryInterface;
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
        $configDefinitions = $this->configDefinitionRegistry->getConfigDefinitions($type);
        foreach ($configDefinitions as $configDefinition) {
            $configIdentifier = $configDefinition->getIdentifier();
            if (!isset($configStructs[$configIdentifier])) {
                continue;
            }

            $this->validate(
                $configStructs[$configIdentifier],
                array(
                    new Constraints\Type(array('type' => ConfigStruct::class)),
                )
            );

            $this->validate(
                $configStructs[$configIdentifier],
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
