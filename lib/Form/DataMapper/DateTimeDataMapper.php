<?php

namespace Netgen\BlockManager\Form\DataMapper;

use DateTimeInterface;
use Symfony\Component\Form\DataMapperInterface;

/**
 * Mapper used to convert to and from the DateTimeInterface object to the Symfony form structure.
 */
final class DateTimeDataMapper implements DataMapperInterface
{
    public function mapDataToForms($data, $forms)
    {
        $forms = iterator_to_array($forms);

        $dateTime = null;
        $timeZone = date_default_timezone_get();

        if ($data instanceof DateTimeInterface) {
            $dateTime = $data->format('Y-m-d H:i:s');
            $timeZone = $data->getTimezone()->getName();
        } elseif (is_array($data)) {
            $dateTime = isset($data['datetime']) ? $data['datetime'] : $dateTime;
            $timeZone = isset($data['timezone']) ? $data['timezone'] : $timeZone;
        }

        $forms['datetime']->setData($dateTime);
        $forms['timezone']->setData($timeZone);
    }

    public function mapFormsToData($forms, &$data)
    {
        $forms = iterator_to_array($forms);
        $dateTime = $forms['datetime']->getData();
        $timeZone = $forms['timezone']->getData();

        if ($dateTime === '') {
            $data = null;

            return;
        }

        $data = array(
            'datetime' => $dateTime,
            'timezone' => $timeZone,
        );
    }
}
