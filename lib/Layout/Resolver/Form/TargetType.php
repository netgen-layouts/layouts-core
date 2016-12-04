<?php

namespace Netgen\BlockManager\Layout\Resolver\Form;

use Netgen\BlockManager\API\Values\LayoutResolver\TargetStruct;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Layout\Resolver\Form\TargetType\MapperInterface;
use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TargetType extends AbstractType
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Form\TargetType\MapperInterface[]
     */
    protected $mappers;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\Form\TargetType\MapperInterface[] $mappers
     */
    public function __construct(array $mappers = array())
    {
        foreach ($mappers as $targetType => $mapper) {
            if (!$mapper instanceof MapperInterface) {
                throw new RuntimeException(
                    sprintf(
                        '"%s" target type form mapper must implement FormMapperInterface interface.',
                        $targetType
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

        $resolver->setRequired('targetType');
        $resolver->setAllowedTypes('targetType', TargetTypeInterface::class);
        $resolver->setAllowedTypes('data', TargetStruct::class);
    }

    /**
     * Builds the form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder The form builder
     * @param array $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface $targetType */
        $targetType = $options['targetType'];

        if (!isset($this->mappers[$targetType->getType()])) {
            throw new RuntimeException(
                sprintf(
                    'Form mapper for "%s" target type does not exist.',
                    $targetType->getType()
                )
            );
        }

        $mapper = $this->mappers[$targetType->getType()];

        $defaultOptions = array(
            'required' => true,
            'label' => sprintf('layout_resolver.target.%s', $targetType->getType()),
            'property_path' => 'value',
            'constraints' => $targetType->getConstraints(),
            'error_bubbling' => false,
        );

        $valueForm = $builder->create(
            'value',
            $mapper->getFormType(),
            $mapper->mapOptions(
                $targetType
            ) + $defaultOptions
        );

        $mapper->handleForm($valueForm, $targetType);

        $builder->add($valueForm);
    }
}
