<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Form\Admin\Type;

use Netgen\Layouts\Validator\Constraint\LayoutName;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraints;

final class CopyRuleType extends AbstractType
{
    private AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('translation_domain', 'nglayouts_forms');

        $resolver->setDefault(
            'validation_groups',
            static fn (FormInterface $form): array => ((bool) $form->get('copy_layout')->getData()) ?
                ['Default', 'CopyLayout'] :
                ['Default']
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $canCopyLayout = $this->authorizationChecker->isGranted('nglayouts:layout:add');

        $builder->add(
            'copy_layout',
            CheckboxType::class,
            [
                'label' => 'rule.copy.copy_layout',
                'data' => $canCopyLayout,
                'required' => false,
                'disabled' => !$canCopyLayout,
            ]
        );

        $builder->add(
            'layout_name',
            TextType::class,
            [
                'label' => 'rule.copy.layout_name',
                'disabled' => !$canCopyLayout,
                'constraints' => [
                    new Constraints\NotBlank(['groups' => ['CopyLayout']]),
                    new LayoutName(['groups' => ['CopyLayout']]),
                ],
            ]
        );
    }
}
