<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer;

use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Throwable;

final class ThrowableNormalizer implements NormalizerInterface
{
    public function __construct(
        private bool $outputDebugInfo,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $data = [
            'code' => $object->getCode(),
            'message' => $object->getMessage(),
        ];

        if ($object instanceof HttpExceptionInterface) {
            $statusCode = $object->getStatusCode();
            if (isset(Response::$statusTexts[$statusCode])) {
                $data['status_code'] = $statusCode;
                $data['status_text'] = Response::$statusTexts[$statusCode];
            }
        }

        if ($this->outputDebugInfo) {
            $flattenException = FlattenException::createFromThrowable($object->getPrevious() ?? $object);

            $data['debug'] = [
                'file' => $flattenException->getFile(),
                'line' => $flattenException->getLine(),
                'trace' => $flattenException->getTrace(),
            ];
        }

        return $data;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Throwable;
    }

    /**
     * @return array<class-string, bool>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            Throwable::class => false,
        ];
    }
}
