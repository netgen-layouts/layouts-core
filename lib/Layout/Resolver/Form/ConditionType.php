<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver\Form;

use Netgen\Layouts\API\Values\LayoutResolver\ConditionStruct;
use Netgen\Layouts\Exception\Layout\ConditionTypeException;
use Netgen\Layouts\Layout\Resolver\ConditionTypeInterface;
use Netgen\Layouts\Layout\Resolver\Form\ConditionType\MapperInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ConditionType extends AbstractType
{
    private ContainerInterface $mappers;

    public function __construct(ContainerInterface $mappers)
    {
        $this->mappers = $mappers;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('translation_domain', 'nglayouts_forms');

        $resolver->setRequired('condition_type');
        $resolver->setAllowedTypes('condition_type', ConditionTypeInterface::class);
        $resolver->setAllowedTypes('data', ConditionStruct::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var \Netgen\Layouts\Layout\Resolver\ConditionTypeInterface $conditionType */
        $conditionType = $options['condition_type'];

        $mapper = $this->getMapper($conditionType::getType());

        $defaultOptions = [
            'label' => false,
            'required' => true,
            'property_path' => 'value',
            'constraints' => $conditionType->getConstraints(),
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
        $view->vars['condition_type'] = $options['condition_type'];
    }

    /**
     * Returns the mapper for provided condition type from the collection.
     *
     * @throws \Netgen\Layouts\Exception\Layout\ConditionTypeException If the mapper does not exist or is not of correct type
     */
    private function getMapper(string $conditionType): MapperInterface
    {
        if (!$this->mappers->has($conditionType)) {
            throw ConditionTypeException::noFormMapper($conditionType);
        }

        $mapper = $this->mappers->get($conditionType);
        if (!$mapper instanceof MapperInterface) {
            throw ConditionTypeException::noFormMapper($conditionType);
        }

        return $mapper;
    }
}
