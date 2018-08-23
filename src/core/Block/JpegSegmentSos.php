<?php

namespace ExifEye\core\Block;

use ExifEye\core\DataWindow;
use ExifEye\core\Entry\Core\Undefined;

/**
 * Class representing a JPEG SOS segment.
 */
class JpegSegmentSos extends JpegSegmentBase
{
    /**
     * {@inheritdoc}
     */
    public function loadFromData(DataWindow $data_window, $offset = 0, array $options = [])
    {
        // Load data in an Undefined entry.
        $entry = new Undefined($this, [$data_window->getBytes()]);
        $entry->debug("Scan data: {text}", ['text' => $entry->toString()]);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toDumpArray()
    {
        $dump = parent::toDumpArray();

        // xx only if not exif
        unset($dump['elements']['entry'][0]['value']);
        unset($dump['elements']['entry'][0]['clear_value']);

        return $dump;
    }
}
