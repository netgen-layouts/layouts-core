<?php

namespace Netgen\BlockManager\Form;

use Netgen\BlockManager\Form\DataMapper\DateTimeDataMapper;
use Netgen\BlockManager\Utils\DateTimeUtils;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType as BaseDateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DateTimeType extends AbstractType
{
    use ChoicesAsValuesTrait;

    private static $html5Format = "yyyy-MM-dd'T'HH:mm";

    /**
     * @var array
     */
    private $timeZoneList = [];

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['use_datetime']);
        $resolver->setAllowedTypes('use_datetime', 'bool');
        $resolver->setDefault('use_datetime', true);

        $resolver->setDefault('error_bubbling', false);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setDataMapper(new DateTimeDataMapper($options['use_datetime']));

        $builder->add(
            'datetime',
            BaseDateTimeType::class,
            [
                'label' => false,
                'format' => self::$html5Format,
                'widget' => 'single_text',
                'html5' => false,
                'input' => 'string',
                'empty_data' => '',
                'property_path' => 'datetime',
            ]
        );

        $builder->add(
            'timezone',
            ChoiceType::class,
            [
                'label' => false,
                'choices' => $this->getTimeZoneList(),
                'choice_translation_domain' => false,
                'property_path' => 'timezone',
            ] + $this->getChoicesAsValuesOption()
        );
    }

    public function getBlockPrefix()
    {
        return 'ngbm_datetime';
    }

    /**
     * Returns the formatted list of all timezones, separated by regions.
     *
     * @return array
     */
    private function getTimeZoneList()
    {
        if (empty($this->timeZoneList)) {
            $this->timeZoneList = DateTimeUtils::getTimeZoneList();
        }

        return $this->timeZoneList;
    }
}
