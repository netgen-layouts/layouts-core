<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\TestCase;

use function array_map;
use function get_object_vars;
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
        $data = (fn (): array => get_object_vars($this))->call($object);

        ksort($data);

        if (!$recursive) {
            return $data;
        }

        return $this->exportArray($data, $recursive);
    }

    /**
     * @param object[] $objects
     *
     * @return array<array<string, mixed>>
     */
    private function exportObjectList(array $objects, bool $recursive = false): array
    {
        return array_map(
            fn (object $object): array => $this->exportObject($object, $recursive),
            $objects,
        );
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
