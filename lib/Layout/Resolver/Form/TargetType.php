<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver\Form;

use Netgen\Layouts\API\Values\LayoutResolver\TargetStruct;
use Netgen\Layouts\Exception\Layout\TargetTypeException;
use Netgen\Layouts\Layout\Resolver\Form\TargetType\MapperInterface;
use Netgen\Layouts\Layout\Resolver\TargetTypeInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TargetType extends AbstractType
{
    private ContainerInterface $mappers;

    public function __construct(ContainerInterface $mappers)
    {
        $this->mappers = $mappers;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('translation_domain', 'nglayouts_forms');

        $resolver->setRequired('target_type');
        $resolver->setAllowedTypes('target_type', TargetTypeInterface::class);
        $resolver->setAllowedTypes('data', TargetStruct::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var \Netgen\Layouts\Layout\Resolver\TargetTypeInterface $targetType */
        $targetType = $options['target_type'];

        $mapper = $this->getMapper($targetType::getType());

        $defaultOptions = [
            'label' => false,
            'required' => true,
            'property_path' => 'value',
            'constraints' => $targetType->getConstraints(),
            'error_bubbling' => false,
        ];

        $valueForm = $builder->create(
            'value',
            $mapper->getFormType(),
            $mapper->getFormOptions() + $defaultOptions,
        );

        $mapper->handleForm($valueForm);

        $builder->add($valueForm);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['target_type'] = $options['target_type'];
    }

    /**
     * Returns the mapper for provided target type from the collection.
     *
     * @throws \Netgen\Layouts\Exception\Layout\TargetTypeException If the mapper does not exist or is not of correct type
     */
    private function getMapper(string $targetType): MapperInterface
    {
        if (!$this->mappers->has($targetType)) {
            throw TargetTypeException::noFormMapper($targetType);
        }

        $mapper = $this->mappers->get($targetType);
        if (!$mapper instanceof MapperInterface) {
            throw TargetTypeException::noFormMapper($targetType);
        }

        return $mapper;
    }
}
