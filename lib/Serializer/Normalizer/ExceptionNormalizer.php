<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Serializer\Normalizer;

use Exception;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ExceptionNormalizer implements NormalizerInterface
{
    /**
     * @var bool
     */
    private $outputDebugInfo;

    public function __construct(bool $outputDebugInfo)
    {
        $this->outputDebugInfo = $outputDebugInfo;
    }

    public function normalize($object, $format = null, array $context = [])
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
            $debugException = $object;
            if ($object->getPrevious() instanceof Exception) {
                $debugException = $object->getPrevious();
            }

            $debugException = FlattenException::create($debugException);

            $data['debug'] = [
                'file' => $debugException->getFile(),
                'line' => $debugException->getLine(),
                'trace' => $debugException->getTrace(),
            ];
        }

        return $data;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Exception;
    }
}
