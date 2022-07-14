<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\TestCase;

use Netgen\Layouts\Utils\Hydrator;

use function is_array;
use function is_object;
use function ksort;

trait ExportObjectTrait
{
    /**
     * @return array<string, mixed>
     */
    private function exportObject(object $object, bool $recursive = false): array
    {
        $data = (new Hydrator())->extract($object);
        ksort($data);

        if (!$recursive) {
            return $data;
        }

        return $this->exportArray($data, $recursive);
    }

    /**
     * @param array<object> $objects
     *
     * @return array<array<string, mixed>>
     */
    private function exportObjectList(array $objects, bool $recursive = false): array
    {
        $data = [];

        foreach ($objects as $key => $object) {
            if (!is_object($object)) {
                continue;
            }

            $data[$key] = $this->exportObject($object, $recursive);
        }

        return $data;
    }

    /**
     * @param mixed[] $data
     *
     * @return mixed[]
     */
    private function exportArray(array $data, bool $recursive = false): array
    {
        $exportedData = [];

        foreach ($data as $key => $value) {
            if ($recursive && is_array($value)) {
                $exportedData[$key] = $this->exportArray($value, $recursive);

                continue;
            }

            if ($recursive && is_object($value)) {
                $exportedData[$key] = $this->exportObject($value, $recursive);

                continue;
            }

            $exportedData[$key] = $value;
        }

        return $exportedData;
    }
}
