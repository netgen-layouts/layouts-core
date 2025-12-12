<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer;

use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Throwable;

use function array_key_exists;

final class ThrowableNormalizer implements NormalizerInterface
{
    public function __construct(
        private bool $outputDebugInfo,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        $normalizedData = [
            'code' => $data->getCode(),
            'message' => $data->getMessage(),
        ];

        if ($data instanceof HttpExceptionInterface) {
            $statusCode = $data->getStatusCode();
            if (array_key_exists($statusCode, Response::$statusTexts)) {
                $normalizedData['status_code'] = $statusCode;
                $normalizedData['status_text'] = Response::$statusTexts[$statusCode];
            }
        }

        if ($this->outputDebugInfo) {
            $flattenException = FlattenException::createFromThrowable($data->getPrevious() ?? $data);

            $normalizedData['debug'] = [
                'file' => $flattenException->getFile(),
                'line' => $flattenException->getLine(),
                'trace' => $flattenException->getTrace(),
            ];
        }

        return $normalizedData;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Throwable;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Throwable::class => false,
        ];
    }
}
