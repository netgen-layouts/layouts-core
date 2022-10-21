<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Form;

use Generator;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\API\Values\Collection\ItemUpdateStruct;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ItemViewTypeEditType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('translation_domain', 'nglayouts_forms');

        $resolver->setRequired('item');
        $resolver->setRequired('block');

        $resolver->setAllowedTypes('item', Item::class);
        $resolver->setAllowedTypes('block', Block::class);
        $resolver->setAllowedTypes('data', ItemUpdateStruct::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'view_type',
            ChoiceType::class,
            [
                'required' => false,
                'label' => 'collection_item.view_type',
                'property_path' => 'viewType',
                'choices' => (static function () use ($options): Generator {
                    yield '<No override>' => '';

                    $block = $options['block'];
                    foreach ($block->getDefinition()->getViewType($block->getViewType(), $block)->getItemViewTypes() as $itemViewType) {
                        yield $itemViewType->getName() => $itemViewType->getIdentifier();
                    }
                })(),
            ],
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['item'] = $options['item'];
        $view->vars['block'] = $options['block'];
    }
}
