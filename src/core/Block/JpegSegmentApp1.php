<?php

namespace ExifEye\core\Block;

use ExifEye\core\DataWindow;
use ExifEye\core\Entry\Core\Undefined;

/**
 * Class representing a JPEG APP1 segment.
 */
class JpegSegmentApp1 extends JpegSegmentBase
{
    /**
     * {@inheritdoc}
     */
    public function loadFromData(DataWindow $data_window, $offset = 0, array $options = [])
    {
        $this->debug("START... Loading");

        // Read the length of the segment. The length includes the two bytes
        // used to store the length.
        $this->components = $data_window->getShort($offset);

        if (Exif::isExifSegment($data_window, $offset + 2)) {
            $exif = new Exif($this);
            $ret = $exif->loadFromData($data_window, $offset + 2);
        } else {
            // We store the data as normal JPEG content if it could not be
            // parsed as Exif data.
            $entry = new Undefined($this, [$data_window->getBytes($offset + 2, $this->components - 2)]);
            $entry->debug("Exif header not found. Loaded {text}", ['text' => $entry->toString()]);
        }

        $this->debug(".....END Loading");

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
