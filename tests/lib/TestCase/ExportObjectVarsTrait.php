<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\TestCase;

trait ExportObjectVarsTrait
{
    /**
     * @param object $object
     *
     * @return array
     */
    private function exportObjectVars($object): array
    {
        return $this->getExporter()->call($object);
    }

    /**
     * @param array $object
     *
     * @return array
     */
    private function exportObjectArrayVars(array $objects): array
    {
        $data = [];

        $exporter = $this->getExporter();

        foreach ($objects as $key => $object) {
            if (!is_object($object)) {
                continue;
            }

            $data[$key] = $exporter->call($object);
        }

        return $data;
    }

    private function getExporter()
    {
        return function (): array {
            return get_object_vars($this);
        };
    }
}
