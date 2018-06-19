<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\TestCase;

trait ExportObjectTrait
{
    /**
     * @param object $object
     * @param bool $recursive
     *
     * @return array
     */
    private function exportObject($object, bool $recursive = false): array
    {
        $data = (function (): array { return get_object_vars($this); })->call($object);

        if (!$recursive) {
            return $data;
        }

        return $this->exportArray($data, $recursive);
    }

    /**
     * @param object[] $objects
     * @param bool $recursive
     *
     * @return array
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
