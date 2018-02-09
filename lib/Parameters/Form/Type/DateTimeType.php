<?php

namespace Netgen\BlockManager\Parameters\Form\Type;

use DateTimeImmutable;
use DateTimeZone;
use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Form\ChoicesAsValuesTrait;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType as BaseDateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DateTimeType extends AbstractType
{
    use ChoicesAsValuesTrait;

    const HTML5_FORMAT = "yyyy-MM-dd'T'HH:mm";

    /**
     * @var array
     */
    private $timeZoneList = array();

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('translation_domain', 'ngbm_forms');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'datetime',
            BaseDateTimeType::class,
            array(
                'label' => 'datetime.datetime',
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
                'label' => 'datetime.timezone',
                'choices' => $this->getTimeZoneList(),
                'choice_translation_domain' => false,
                'property_path' => 'timezone',
            )
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
            foreach (DateTimeZone::listIdentifiers() as $timeZone) {
                list($region, $name) = $this->parseTimeZone($timeZone);

                $offset = $this->buildOffsetString($timeZone);
                $name = sprintf('%s (%s)', str_replace('_', ' ', $name), $offset);

                $this->timeZoneList[$region][$name] = $timeZone;
            }
        }

        return $this->timeZoneList;
    }

    /**
     * Returns the array with human readable region and timezone name for the provided
     * timezone identifier.
     *
     * @param string $timeZone
     *
     * @return array
     */
    private function parseTimeZone($timeZone)
    {
        $parts = explode('/', $timeZone);

        if (count($parts) > 2) {
            return array($parts[0], $parts[1] . ' / ' . $parts[2]);
        } elseif (count($parts) > 1) {
            return array($parts[0], $parts[1]);
        }

        return array('Other', $parts[0]);
    }

    /**
     * Returns the formatted UTC offset for the provided timezone identifier
     * in the form of (+/-)HH:mm.
     *
     * @param string $timeZone
     *
     * @return string
     */
    private function buildOffsetString($timeZone)
    {
        $currentTimeInZone = new DateTimeImmutable('now', new DateTimeZone($timeZone));

        $offset = $currentTimeInZone->getOffset();

        $hours = intdiv($offset, 3600);
        $minutes = (int) (($offset % 3600) / 60);

        return sprintf('%s%02d:%02d', $offset >= 0 ? '+' : '-', abs($hours), abs($minutes));
    }
}
