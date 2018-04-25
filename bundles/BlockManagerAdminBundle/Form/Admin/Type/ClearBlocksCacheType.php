<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Form\Admin\Type;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Form\ChoicesAsValuesTrait;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ClearBlocksCacheType extends AbstractType
{
    use ChoicesAsValuesTrait;

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(['blocks']);
        $resolver->setAllowedTypes('blocks', 'array');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'blocks',
            ChoiceType::class,
            [
                'choices' => $options['blocks'],
                'choice_value' => 'id',
                'choice_label' => function (Block $block) {
                    $blockName = $block->getName();

                    return !empty($blockName) ? $blockName : ' ';
                },
                'translation_domain' => false,
                'choice_translation_domain' => false,
                'required' => true,
                'multiple' => true,
                'expanded' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ] + $this->getChoicesAsValuesOption()
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $blocks = [];
        foreach ($options['blocks'] as $block) {
            $blocks[$block->getId()] = $block;
        }

        $view->vars['blocks'] = $blocks;
    }
}
