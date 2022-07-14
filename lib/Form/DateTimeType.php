<?php

declare(strict_types=1);

namespace Netgen\Layouts\Form;

use Netgen\Layouts\Form\DataMapper\DateTimeDataMapper;
use Netgen\Layouts\Utils\DateTimeUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType as BaseDateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function count;

final class DateTimeType extends AbstractType
{
    private const HTML5_FORMAT = "yyyy-MM-dd'T'HH:mm";

    /**
     * @var array<string, array<string, string>>
     */
    private array $timeZoneList = [];

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('translation_domain', 'nglayouts_forms');

        $resolver->setRequired(['use_datetime']);
        $resolver->setAllowedTypes('use_datetime', 'bool');
        $resolver->setDefault('use_datetime', true);

        $resolver->setDefault('error_bubbling', false);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setDataMapper(new DateTimeDataMapper($options['use_datetime']));

        $builder->add(
            'datetime',
            BaseDateTimeType::class,
            [
                'label' => false,
                'format' => self::HTML5_FORMAT,
                'widget' => 'single_text',
                'html5' => false,
                'input' => 'string',
                'empty_data' => '',
                'property_path' => 'datetime',
            ],
        );

        $builder->add(
            'timezone',
            ChoiceType::class,
            [
                'label' => false,
                'choices' => $this->getTimeZoneList(),
                'choice_translation_domain' => false,
                'property_path' => 'timezone',
            ],
        );
    }

    public function getBlockPrefix(): string
    {
        return 'nglayouts_datetime';
    }

    /**
     * Returns the formatted list of all timezones, separated by regions.
     *
     * @return array<string, array<string, string>>
     */
    private function getTimeZoneList(): array
    {
        if (count($this->timeZoneList) === 0) {
            $this->timeZoneList = DateTimeUtils::getTimeZoneList();
        }

        return $this->timeZoneList;
    }
}
