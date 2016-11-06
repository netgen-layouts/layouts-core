<?php

namespace Netgen\BlockManager\Layout\Resolver\Form;

use Netgen\BlockManager\API\Values\ConditionStruct;
use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\MapperInterface;
use Netgen\BlockManager\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Netgen\BlockManager\Exception\RuntimeException;

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
                throw new RuntimeException(
                    sprintf(
                        '"%s" condition type form mapper must implement FormMapperInterface interface.',
                        $conditionType
                    )
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
            throw new RuntimeException(
                sprintf(
                    'Form mapper for "%s" condition type does not exist.',
                    $conditionType->getType()
                )
            );
        }

        $mapper = $this->mappers[$conditionType->getType()];

        $valueForm = $builder->create(
            'value',
            $mapper->getFormType(),
            array(
                'property_path' => 'value',
            ) + $mapper->mapOptions($conditionType)
        );

        $mapper->handleForm($valueForm);

        $builder->add($valueForm);
    }
}
