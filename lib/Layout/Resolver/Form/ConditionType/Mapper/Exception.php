<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Response;

use function count;
use function sprintf;

final class Exception extends Mapper
{
    /**
     * @var array<string, int>
     */
    private array $statusCodes = [];

    public function getFormType(): string
    {
        return ChoiceType::class;
    }

    public function getFormOptions(): array
    {
        return [
            'multiple' => true,
            'required' => false,
            'choices' => $this->buildErrorCodes(),
        ];
    }

    /**
     * Builds the formatted list of all available error codes (those which are in 4xx and 5xx range).
     *
     * @return array<string, int>
     */
    private function buildErrorCodes(): array
    {
        if (count($this->statusCodes) === 0) {
            foreach (Response::$statusTexts as $statusCode => $statusText) {
                if ($statusCode >= 400 && $statusCode < 600) {
                    $this->statusCodes[sprintf('%d (%s)', $statusCode, $statusText)] = (int) $statusCode;
                }
            }
        }

        return $this->statusCodes;
    }
}
