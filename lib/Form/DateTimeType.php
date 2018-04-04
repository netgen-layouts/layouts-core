<?php

namespace Netgen\BlockManager\Form;

use Netgen\BlockManager\Form\DataMapper\DateTimeDataMapper;
use Netgen\BlockManager\Utils\DateTimeUtils;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType as BaseDateTimeType;
use Symfony\Component\Form\FormBuilderInterface;

final class DateTimeType extends AbstractType
{
    use ChoicesAsValuesTrait;

    const HTML5_FORMAT = "yyyy-MM-dd'T'HH:mm";

    /**
     * @var array
     */
    private $timeZoneList = array();

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setDataMapper(new DateTimeDataMapper());

        $builder->add(
            'datetime',
            BaseDateTimeType::class,
            array(
                'label' => false,
                'format' => static::HTML5_FORMAT,
                'widget' => 'single_text',
                'html5' => false,
                'input' => 'string',
                'empty_data' => '',
                'property_path' => 'datetime',
            )
        );

        $builder->add(
            'timezone',
            ChoiceType::class,
            array(
                'label' => false,
                'choices' => $this->getTimeZoneList(),
                'choice_translation_domain' => false,
                'property_path' => 'timezone',
            ) + $this->getChoicesAsValuesOption()
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
