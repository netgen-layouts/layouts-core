<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\TestCase;

use Netgen\Layouts\Utils\Hydrator;

trait ExportObjectTrait
{
    private function exportObject(object $object, bool $recursive = false): array
    {
        $data = (new Hydrator())->extract($object);

        if (!$recursive) {
            return $data;
        }

        return $this->exportArray($data, $recursive);
    }

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
