<?php

namespace Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\BlockManager\Form\ChoicesAsValuesTrait;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Response;

final class Exception extends Mapper
{
    use ChoicesAsValuesTrait;

    /**
     * @var array
     */
    private $statusCodes = [];

    public function getFormType()
    {
        return ChoiceType::class;
    }

    public function getFormOptions()
    {
        return [
            'multiple' => true,
            'required' => false,
            'choices' => $this->buildErrorCodes(),
        ] + $this->getChoicesAsValuesOption();
    }

    /**
     * Builds the formatted list of all available error codes (those which are in 4xx and 5xx range).
     *
     * @return array
     */
    private function buildErrorCodes()
    {
        if (empty($this->statusCodes)) {
            foreach (Response::$statusTexts as $statusCode => $statusText) {
                if ($statusCode >= 400 && $statusCode < 600) {
                    $this->statusCodes[sprintf('%d (%s)', $statusCode, $statusText)] = $statusCode;
                }
            }
        }

        return $this->statusCodes;
    }
}
