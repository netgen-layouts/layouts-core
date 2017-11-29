<?php

namespace Netgen\BlockManager\Transfer\Input;

use Netgen\BlockManager\Exception\Transfer\DataNotAcceptedException;
use Netgen\BlockManager\Transfer\Descriptor;
use Netgen\BlockManager\Transfer\Input\DataHandler\LayoutDataHandler;

/**
 * Importer creates Netgen Layouts entities from the serialized JSON data.
 */
final class Importer
{
    /**
     * @var \Netgen\BlockManager\Transfer\Input\DataHandler\LayoutDataHandler
     */
    private $layoutDataHandler;

    public function __construct(LayoutDataHandler $layoutDataHandler)
    {
        $this->layoutDataHandler = $layoutDataHandler;
    }

    /**
     * Create a new layout from the given $data array.
     *
     * @param array $data
     *
     * @throws \Netgen\BlockManager\Exception\Transfer\DataNotAcceptedException If $data is not accepted for import
     * @throws \Exception If thrown by the underlying API
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function importLayout(array $data)
    {
        $this->acceptLayout($data);

        return $this->layoutDataHandler->createLayout($data);
    }

    /**
     * Checks that given $data is in the accepted format.
     *
     * @param array $data
     *
     * @throws \Netgen\BlockManager\Exception\Transfer\DataNotAcceptedException If $data is not accepted
     */
    private function acceptLayout(array $data)
    {
        if (!array_key_exists('__format', $data)) {
            throw DataNotAcceptedException::noFormatInformation();
        }

        $actualType = array_key_exists('type', $data['__format']) ? $data['__format']['type'] : null;
        $actualVersion = array_key_exists('version', $data['__format']) ? $data['__format']['version'] : null;

        if ($actualType !== Descriptor::LAYOUT_FORMAT_TYPE) {
            throw DataNotAcceptedException::typeNotAccepted(Descriptor::LAYOUT_FORMAT_TYPE, $actualType);
        }

        if ($actualVersion !== Descriptor::LAYOUT_FORMAT_VERSION) {
            throw DataNotAcceptedException::versionNotAccepted(Descriptor::LAYOUT_FORMAT_VERSION, $actualVersion);
        }
    }
}
