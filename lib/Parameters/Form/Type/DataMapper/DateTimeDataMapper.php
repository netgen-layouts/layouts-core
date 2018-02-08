<?php

namespace Netgen\BlockManager\Parameters\Form\Type\DataMapper;

use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
use Netgen\BlockManager\Parameters\Value\DateTimeValue;
use Symfony\Component\Form\DataMapperInterface;

/**
 * Mapper used to convert to and from the DateTimeValue object to the Symfony form structure.
 */
final class DateTimeDataMapper implements DataMapperInterface
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterDefinitionInterface
     */
    private $parameterDefinition;

    public function __construct(ParameterDefinitionInterface $parameterDefinition)
    {
        $this->parameterDefinition = $parameterDefinition;
    }

    public function mapDataToForms($data, $forms)
    {
        $forms = iterator_to_array($forms);

        $dateTime = null;
        $timeZone = date_default_timezone_get();

        if ($data instanceof DateTimeValue && !empty($data->getDateTime())) {
            $dateTime = $data->getDateTime();
            $timeZone = $data->getTimeZone();
        }

        $forms['datetime']->setData($dateTime);
        $forms['timezone']->setData($timeZone);
    }

    public function mapFormsToData($forms, &$data)
    {
        $forms = iterator_to_array($forms);
        $dateTime = $forms['datetime']->getData();

        $data = null;
        if (!empty($dateTime)) {
            $data = array(
                'datetime' => $dateTime,
                'timezone' => $forms['timezone']->getData(),
            );
        }

        $data = $this->parameterDefinition->getType()->fromHash($this->parameterDefinition, $data);
    }
}
