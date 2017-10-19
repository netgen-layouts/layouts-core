<?php

namespace Netgen\BlockManager\Transfer\Input;

use Netgen\BlockManager\Exception\Transfer\DataNotAcceptedException;
use Netgen\BlockManager\Transfer\Descriptor;
use Netgen\BlockManager\Transfer\Input\DataHandler\LayoutDataHandler;

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
     * Create a new Layout from the given $data array.
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
     *
     * @return void
     */
    private function acceptLayout(array $data)
    {
        if (!array_key_exists('__format', $data)) {
            throw DataNotAcceptedException::noFormatInformation();
        }

        $actualType = $data['__format']['type'];
        $expectedType = Descriptor::LAYOUT_FORMAT_TYPE;
        $actualVersion = $data['__format']['version'];
        $expectedVersion = Descriptor::LAYOUT_FORMAT_VERSION;

        if ($actualType !== $expectedType) {
            throw DataNotAcceptedException::typeNotAccepted($expectedType, $actualType);
        }

        if ($actualVersion !== $expectedVersion) {
            throw DataNotAcceptedException::versionNotAccepted($expectedVersion, $actualVersion);
        }
    }
}
