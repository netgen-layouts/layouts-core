<?php

namespace Netgen\BlockManager\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class ExceptionNormalizer implements NormalizerInterface
{
    /**
     * @var bool
     */
    protected $outputDebugInfo = false;

    /**
     * Sets if the normalizer should output debugging information.
     *
     * @param bool $outputDebugInfo
     */
    public function setOutputDebugInfo($outputDebugInfo = false)
    {
        $this->outputDebugInfo = (bool)$outputDebugInfo;
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param \Exception $object
     * @param string $format
     * @param array $context
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $data = array(
            'code' => $object->getCode(),
            'message' => $object->getMessage(),
        );

        if ($object instanceof HttpException) {
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

            // We disable serializing trace arguments as it can lead to
            // recursion calls in serializer.
            $trace = $debugException->getTrace();
            foreach ($trace as &$traceItem) {
                $traceItem['args'] = array();
            }

            $data['debug'] = array(
                'file' => $debugException->getFile(),
                'line' => $debugException->getLine(),
                'trace' => $trace,
            );
        }

        return $data;
    }

    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed $data
     * @param string $format
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Exception;
    }
}
