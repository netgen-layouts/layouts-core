<?php

namespace Netgen\BlockManager\Layout\Resolver\Form;

use Netgen\BlockManager\API\Values\LayoutResolver\ConditionStruct;
use Netgen\BlockManager\Exception\InvalidInterfaceException;
use Netgen\BlockManager\Exception\Layout\ConditionTypeException;
use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\MapperInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConditionType extends AbstractType
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\MapperInterface[]
     */
    protected $mappers;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\MapperInterface[] $mappers
     */
    public function __construct(array $mappers = array())
    {
        foreach ($mappers as $conditionType => $mapper) {
            if (!$mapper instanceof MapperInterface) {
                throw new InvalidInterfaceException(
                    'Form mapper for condition type',
                    $conditionType,
                    MapperInterface::class
                );
            }
        }

        $this->mappers = $mappers;
    }

    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('conditionType');
        $resolver->setAllowedTypes('conditionType', ConditionTypeInterface::class);
        $resolver->setAllowedTypes('data', ConditionStruct::class);
    }

    /**
     * Builds the form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder The form builder
     * @param array $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface $conditionType */
        $conditionType = $options['conditionType'];

        if (!isset($this->mappers[$conditionType->getType()])) {
            throw ConditionTypeException::noFormMapper($conditionType->getType());
        }

        $mapper = $this->mappers[$conditionType->getType()];

        $defaultOptions = array(
            'required' => true,
            'label' => sprintf('layout_resolver.condition.%s', $conditionType->getType()),
            'property_path' => 'value',
            'constraints' => $conditionType->getConstraints(),
            'error_bubbling' => false,
        );

        $valueForm = $builder->create(
            'value',
            $mapper->getFormType(),
            $mapper->mapOptions(
                $conditionType
            ) + $defaultOptions
        );

        $mapper->handleForm($valueForm, $conditionType);

        $builder->add($valueForm);
    }
}
