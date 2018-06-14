<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Form\DataMapper;

use DateTimeInterface;
use Netgen\BlockManager\Utils\DateTimeUtils;
use Symfony\Component\Form\DataMapperInterface;

/**
 * Mapper used to convert to and from the DateTimeInterface object to the Symfony form structure.
 */
final class DateTimeDataMapper implements DataMapperInterface
{
    /**
     * @var bool
     */
    private $useDateTime;

    public function __construct(bool $useDateTime = true)
    {
        $this->useDateTime = $useDateTime;
    }

    public function mapDataToForms($data, $forms): void
    {
        $forms = iterator_to_array($forms);

        $dateTime = null;
        $timeZone = date_default_timezone_get();

        if ($data instanceof DateTimeInterface) {
            $dateTime = $data->format('Y-m-d H:i:s');
            $timeZone = $data->getTimezone()->getName();
        } elseif (is_array($data)) {
            $dateTime = $data['datetime'] ?? $dateTime;
            $timeZone = $data['timezone'] ?? $timeZone;
        }

        $forms['datetime']->setData($dateTime);
        $forms['timezone']->setData($timeZone);
    }

    public function mapFormsToData($forms, &$data): void
    {
        $forms = iterator_to_array($forms);
        $dateTime = $forms['datetime']->getData();
        $timeZone = $forms['timezone']->getData();

        if ($dateTime === '') {
            $data = null;

            return;
        }

        $dateArray = [
            'datetime' => $dateTime,
            'timezone' => $timeZone,
        ];

        $data = $this->useDateTime ?
            DateTimeUtils::createFromArray($dateArray) :
            $dateArray;
    }
}
