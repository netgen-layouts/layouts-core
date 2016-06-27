<?php

namespace Netgen\BlockManager\Layout\Resolver\Form;

use Netgen\BlockManager\API\Values\ConditionStruct;
use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\MapperInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;
use RuntimeException;

class ConditionType extends AbstractType
{
    const TRANSLATION_DOMAIN = 'ngbm_layout_resolver_forms';

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
        foreach ($mappers as $identifier => $mapper) {
            if (!$mapper instanceof MapperInterface) {
                throw new RuntimeException(
                    sprintf(
                        '"%s" condition type form mapper must implement FormMapperInterface interface',
                        $identifier
                    )
                );
            }
        }

        $this->mappers = $mappers;
    }

    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('conditionType');
        $resolver->setAllowedTypes('conditionType', ConditionTypeInterface::class);
        $resolver->setAllowedTypes('data', ConditionStruct::class);
        $resolver->setDefault('translation_domain', self::TRANSLATION_DOMAIN);
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

        if (!isset($this->mappers[$conditionType->getIdentifier()])) {
            throw new RuntimeException(
                sprintf(
                    'Form mapper for "%s" condition type does not exist',
                    $conditionType->getIdentifier()
                )
            );
        }

        $mapper = $this->mappers[$conditionType->getIdentifier()];

        $valueForm = $builder->create(
            'value',
            $mapper->getFormType(),
            array(
                'required' => true,
                'constraints' => $conditionType->getConstraints(),
                'property_path' => 'value',
            ) + $mapper->getOptions()
        );

        $mapper->handleForm($valueForm);

        $builder->add($valueForm);
    }
}
