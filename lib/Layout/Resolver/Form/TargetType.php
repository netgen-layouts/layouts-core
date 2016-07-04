<?php

namespace Netgen\BlockManager\Layout\Resolver\Form;

use Netgen\BlockManager\API\Values\TargetStruct;
use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;
use Netgen\BlockManager\Layout\Resolver\Form\TargetType\MapperInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use RuntimeException;

class TargetType extends AbstractType
{
    const TRANSLATION_DOMAIN = 'ngbm_layout_resolver_forms';

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
                        '"%s" target type form mapper must implement FormMapperInterface interface',
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
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('targetType');
        $resolver->setAllowedTypes('targetType', TargetTypeInterface::class);
        $resolver->setAllowedTypes('data', TargetStruct::class);
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
        /** @var \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface $targetType */
        $targetType = $options['targetType'];

        if (!isset($this->mappers[$targetType->getType()])) {
            throw new RuntimeException(
                sprintf(
                    'Form mapper for "%s" target type does not exist',
                    $targetType->getType()
                )
            );
        }

        $mapper = $this->mappers[$targetType->getType()];

        $valueForm = $builder->create(
            'value',
            $mapper->getFormType(),
            array(
                'property_path' => 'value',
            ) + $mapper->getOptions($targetType)
        );

        $mapper->handleForm($valueForm);

        $builder->add($valueForm);
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefixes default to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix()
    {
        return 'ngbm_target_type';
    }
}
