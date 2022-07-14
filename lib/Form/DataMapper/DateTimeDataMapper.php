<?php

declare(strict_types=1);

namespace Netgen\Layouts\Form\DataMapper;

use DateTimeInterface;
use Netgen\Layouts\Utils\DateTimeUtils;
use Symfony\Component\Form\DataMapperInterface;

use function date_default_timezone_get;
use function is_array;
use function iterator_to_array;

/**
 * Mapper used to convert to and from the DateTimeInterface object to the Symfony form structure.
 */
final class DateTimeDataMapper implements DataMapperInterface
{
    private bool $useDateTime;

    public function __construct(bool $useDateTime = true)
    {
        $this->useDateTime = $useDateTime;
    }

    public function mapDataToForms($viewData, $forms): void
    {
        $forms = iterator_to_array($forms);

        $dateTime = null;
        $timeZone = date_default_timezone_get();

        if ($viewData instanceof DateTimeInterface) {
            $dateTime = $viewData->format('Y-m-d H:i:s');
            $timeZone = $viewData->getTimezone()->getName();
        } elseif (is_array($viewData)) {
            $dateTime = $viewData['datetime'] ?? $dateTime;
            $timeZone = $viewData['timezone'] ?? $timeZone;
        }

        $forms['datetime']->setData($dateTime);
        $forms['timezone']->setData($timeZone);
    }

    public function mapFormsToData($forms, &$viewData): void
    {
        $forms = iterator_to_array($forms);
        $dateTime = $forms['datetime']->getData();
        $timeZone = $forms['timezone']->getData();

        if ($dateTime === '') {
            $viewData = null;

            return;
        }

        $dateArray = [
            'datetime' => $dateTime,
            'timezone' => $timeZone,
        ];

        $viewData = $this->useDateTime ?
            DateTimeUtils::createFromArray($dateArray) :
            $dateArray;
    }
}
