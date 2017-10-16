<?php

namespace Netgen\BlockManager\Transfer\Input;

use Netgen\BlockManager\Transfer\Descriptor;
use Netgen\BlockManager\Transfer\Input\DataHandler\LayoutDataHandler;
use RuntimeException;

/**
 * Importer creates Block Manager entities from the serialized JSON data.
 */
final class Importer
{
    /**
     * @var \Netgen\BlockManager\Transfer\Input\DataHandler\LayoutDataHandler
     */
    private $layoutDataHandler;

    /**
     * @param \Netgen\BlockManager\Transfer\Input\DataHandler\LayoutDataHandler $layoutDataHandler
     */
    public function __construct(LayoutDataHandler $layoutDataHandler)
    {
        $this->layoutDataHandler = $layoutDataHandler;
    }

    /**
     * Create a new Layout from the given $data string.
     *
     * @param string $data
     *
     * @throws \RuntimeException If $data is not accepted for import
     * @throws \Exception If thrown by the underlying API
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function importLayout($data)
    {
        $data = $this->deserialize($data);
        $this->accept($data);

        return $this->layoutDataHandler->createLayout($data);
    }

    /**
     * Checks that given $data is in the accepted format.
     *
     * @param array $data
     *
     * @throws \RuntimeException If $data is not accepted
     *
     * @return void
     */
    private function accept(array $data)
    {
        if (!array_key_exists('__format', $data)) {
            throw new RuntimeException('Could not find format information in the provided data');
        }

        $actualType = $data['__format']['type'];
        $supportedType = Descriptor::LAYOUT_FORMAT_TYPE;
        $actualVersion = $data['__format']['version'];
        $supportedVersion = Descriptor::LAYOUT_FORMAT_VERSION;

        if ($actualType !== $supportedType) {
            throw new RuntimeException(
                "Supported type is '{$supportedVersion}', type '{$actualVersion}' was given"
            );
        }

        if ($actualVersion !== $supportedVersion) {
            throw new RuntimeException(
                "Supported version is {$supportedVersion}, version {$actualVersion} was given"
            );
        }
    }

    /**
     * Deserialize given JSON $string.
     *
     * @param $string
     *
     * @return mixed
     */
    private function deserialize($string)
    {
        return json_decode($string, true);
    }
}
