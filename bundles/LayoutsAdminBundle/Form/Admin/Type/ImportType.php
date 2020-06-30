<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Form\Admin\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

final class ImportType extends AbstractType
{
    /**
     * @var string
     */
    private $maxUploadSize;

    /**
     * @var bool
     */
    private $overwriteExisting;

    public function __construct(string $maxUploadSize, bool $overwriteExisting)
    {
        $this->maxUploadSize = $maxUploadSize;
        $this->overwriteExisting = $overwriteExisting;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('translation_domain', 'nglayouts_forms');
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'file',
            FileType::class,
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
                        ]
                    ),
                ],
            ]
        );

        $builder->add(
            'overwriteExisting',
            CheckboxType::class,
            [
                'required' => false,
                'label' => 'import.overwrite_existing',
                'data' => $this->overwriteExisting,
            ]
        );
    }
}
