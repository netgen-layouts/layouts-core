<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Form\Admin\Type;

use Netgen\Layouts\Transfer\Input\ImportMode;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

final class ImportType extends AbstractType
{
    public function __construct(
        private string $maxUploadSize,
        private string $importMode,
    ) {}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('translation_domain', 'nglayouts_forms');
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'file',
            Type\FileType::class,
            [
                'label' => 'import.file',
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\File(
                        maxSize: $this->maxUploadSize,
                        mimeTypes: [
                            'application/json',
                            // Needs text/plain too: https://github.com/symfony/symfony/issues/37457
                            'text/plain',
                        ],
                        mimeTypesMessage: 'import.file.mime_types_message',
                    ),
                ],
            ],
        );

        $builder->add(
            'import_mode',
            Type\EnumType::class,
            [
                'label' => 'import.import_mode',
                'expanded' => true,
                'data' => $this->importMode,
                'class' => ImportMode::class,
                'choice_label' => static fn (ImportMode $mode): string => match ($mode) {
                    ImportMode::Copy => 'import.import_mode.copy',
                    ImportMode::Overwrite => 'import.import_mode.overwrite',
                    ImportMode::Skip => 'import.import_mode.skip',
                },
            ],
        );
    }
}
