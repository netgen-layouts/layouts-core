<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\TestCase;

use Closure;

trait ExportObjectVarsTrait
{
    /**
     * @param object $object
     * @param bool $recursive
     *
     * @return array
     */
    private function exportObjectVars($object, bool $recursive = false): array
    {
        $data = $this->getExporter()->call($object);

        if (!$recursive) {
            return $data;
        }

        return $this->exportArray($data, $recursive);
    }

    private function exportObjectArrayVars(array $objects, bool $recursive = false): array
    {
        $data = [];

        foreach ($objects as $key => $object) {
            if (!is_object($object)) {
                continue;
            }

            $data[$key] = $this->exportObjectVars($object, $recursive);
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
                $exportedData[$key] = $this->exportObjectVars($value, $recursive);

                continue;
            }

            $exportedData[$key] = $value;
        }

        return $exportedData;
    }

    private function getExporter(): Closure
    {
        return function (): array {
            return get_object_vars($this);
        };
    }
}
