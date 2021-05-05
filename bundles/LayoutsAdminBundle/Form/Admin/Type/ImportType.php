<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Form\Admin\Type;

use Netgen\Layouts\Transfer\Input\ImportOptions;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

final class ImportType extends AbstractType
{
    private string $maxUploadSize;

    private string $importMode;

    public function __construct(string $maxUploadSize, string $importMode)
    {
        $this->maxUploadSize = $maxUploadSize;
        $this->importMode = $importMode;
    }

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
                        [
                            'maxSize' => $this->maxUploadSize,
                            'mimeTypes' => [
                                'application/json',
                                // Needs text/plain too: https://github.com/symfony/symfony/issues/37457
                                'text/plain',
                            ],
                            'mimeTypesMessage' => 'import.file.mime_types_message',
                        ],
                    ),
                ],
            ],
        );

        $builder->add(
            'import_mode',
            Type\ChoiceType::class,
            [
                'label' => 'import.import_mode',
                'expanded' => true,
                'data' => $this->importMode,
                'choices' => [
                    'import.import_mode.copy' => ImportOptions::MODE_COPY,
                    'import.import_mode.overwrite' => ImportOptions::MODE_OVERWRITE,
                    'import.import_mode.skip' => ImportOptions::MODE_SKIP,
                ],
            ],
        );
    }
}
