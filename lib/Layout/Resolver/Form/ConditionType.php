<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Resolver\Form;

use Netgen\BlockManager\API\Values\LayoutResolver\ConditionStruct;
use Netgen\BlockManager\Exception\Layout\ConditionTypeException;
use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\MapperInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ConditionType extends AbstractType
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\MapperInterface[]
     */
    private $mappers;

    /**
     * @param \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\MapperInterface[] $mappers
     */
    public function __construct(array $mappers = [])
    {
        $this->mappers = array_filter(
            $mappers,
            function (MapperInterface $mapper): bool {
                return true;
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('condition_type');
        $resolver->setAllowedTypes('condition_type', ConditionTypeInterface::class);
        $resolver->setAllowedTypes('data', ConditionStruct::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface $conditionType */
        $conditionType = $options['condition_type'];

        if (!isset($this->mappers[$conditionType::getType()])) {
            throw ConditionTypeException::noFormMapper($conditionType::getType());
        }

        $mapper = $this->mappers[$conditionType::getType()];

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
            $mapper->getFormOptions() + $defaultOptions
        );

        $mapper->handleForm($valueForm);

        $builder->add($valueForm);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['condition_type'] = $options['condition_type'];
    }
}
