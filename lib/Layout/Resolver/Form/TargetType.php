<?php

namespace Netgen\BlockManager\Layout\Resolver\Form;

use Netgen\BlockManager\API\Values\LayoutResolver\TargetStruct;
use Netgen\BlockManager\Exception\InvalidInterfaceException;
use Netgen\BlockManager\Exception\Layout\TargetTypeException;
use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Layout\Resolver\Form\TargetType\MapperInterface;
use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TargetType extends AbstractType
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Form\TargetType\MapperInterface[]
     */
    private $mappers;

    /**
     * @param \Netgen\BlockManager\Layout\Resolver\Form\TargetType\MapperInterface[] $mappers
     */
    public function __construct(array $mappers = array())
    {
        foreach ($mappers as $targetType => $mapper) {
            if (!$mapper instanceof MapperInterface) {
                throw new InvalidInterfaceException(
                    'Form mapper for target type',
                    $targetType,
                    MapperInterface::class
                );
            }
        }

        $this->mappers = $mappers;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('targetType');
        $resolver->setAllowedTypes('targetType', TargetTypeInterface::class);
        $resolver->setAllowedTypes('data', TargetStruct::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface $targetType */
        $targetType = $options['targetType'];

        if (!isset($this->mappers[$targetType->getType()])) {
            throw TargetTypeException::noFormMapper($targetType->getType());
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
            $mapper->getFormOptions() + $defaultOptions
        );

        $mapper->handleForm($valueForm);

        $builder->add($valueForm);
    }
}
