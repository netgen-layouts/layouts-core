<?php

namespace Netgen\BlockManager\Parameters\Form\Type;

use Netgen\BlockManager\API\Values\ParameterStruct;
use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Parameters\CompoundParameterInterface;
use Netgen\BlockManager\Parameters\ParameterCollectionInterface;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\BlockManager\Parameters\Registry\FormMapperRegistryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ParametersType extends AbstractType
{
    /**
     * @var \Netgen\BlockManager\Parameters\Registry\FormMapperRegistryInterface
     */
    private $formMapperRegistry;

    public function __construct(FormMapperRegistryInterface $formMapperRegistry)
    {
        $this->formMapperRegistry = $formMapperRegistry;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(
            array(
                'groups',
                'parameter_collection',
                'label_prefix',
            )
        );

        $resolver->setAllowedTypes('groups', 'array');
        $resolver->setAllowedTypes('data', ParameterStruct::class);
        $resolver->setAllowedTypes('parameter_collection', ParameterCollectionInterface::class);
        $resolver->setAllowedTypes('label_prefix', 'string');

        $resolver->setDefault('groups', array());
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Netgen\BlockManager\Parameters\ParameterCollectionInterface $parameterCollection */
        $parameterCollection = $options['parameter_collection'];

        foreach ($parameterCollection->getParameters() as $parameter) {
            if (!$this->includeParameter($parameter, $options['groups'])) {
                continue;
            }

            $parameterName = $parameter->getName();
            $parameterLabel = $parameter->getLabel();

            $mapper = $this->formMapperRegistry->getFormMapper(
                $parameter->getType()->getIdentifier()
            );

            $defaultOptions = array(
                'label' => $parameterLabel === null ?
                    $options['label_prefix'] . '.' . $parameterName :
                    $parameterLabel,
                'property_path' => 'parameterValues[' . $parameterName . ']',
                'ngbm_parameter' => $parameter,
            );

            $parameterForm = $builder->create(
                $parameterName,
                $mapper->getFormType(),
                $mapper->mapOptions(
                    $parameter
                ) + $defaultOptions
            );

            $mapper->handleForm($parameterForm, $parameter);

            if ($parameter instanceof CompoundParameterInterface) {
                $this->buildForm(
                    $parameterForm,
                    array(
                        'parameter_collection' => $parameter,
                    ) + $options
                );
            }

            $builder->add($parameterForm);
        }
    }

    /**
     * Returns if the parameter will be included in the form based on provided groups.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param array $groups
     *
     * @return bool
     */
    private function includeParameter(ParameterInterface $parameter, array $groups = array())
    {
        $parameterGroups = $parameter->getGroups();

        if (empty($parameterGroups) || empty($groups)) {
            return true;
        }

        return !empty(array_intersect($parameterGroups, $groups));
    }
}
